<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\ClinicSetting;
use App\Models\Patient;
use App\Models\QueueEntry;
use App\Models\QueueStatusHistory;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class QueueManagementService
{
    protected EstimationService $estimationService;

    public function __construct(EstimationService $estimationService)
    {
        $this->estimationService = $estimationService;
    }

    /**
     * Check-in an existing appointment.
     */
    public function checkInAppointment(int $appointmentId, ?User $actor = null): QueueEntry
    {
        $lateCheckInMessage = null;

        $queueEntry = DB::transaction(function () use ($appointmentId, $actor, &$lateCheckInMessage) {
            $appointment = Appointment::with(['patient', 'service', 'queueEntry'])
                ->lockForUpdate()
                ->findOrFail($appointmentId);

            $today = Carbon::today();
            if ($appointment->appointment_date->format('Y-m-d') !== $today->format('Y-m-d')) {
                throw new InvalidArgumentException("Reservasi hanya dapat di-check-in pada tanggal kunjungan ({$appointment->appointment_date->format('d/m/Y')}).");
            }

            // Check if already checked in (Idempotency)
            if ($appointment->status === 'checked_in') {
                $existingQueue = $appointment->queueEntry ?? QueueEntry::where('appointment_id', $appointment->id)->first();
                if ($existingQueue) {
                    if ($existingQueue->status === 'waiting_triage') {
                        $existingQueue->update(['status' => 'waiting_call']);
                        $this->logStatusChange($existingQueue, 'waiting_triage', 'waiting_call', 'Pembaruan status antrean ke siap dipanggil dokter', $actor);
                        $this->estimationService->recalculateEstimations($existingQueue->service_id, $today->format('Y-m-d'));
                    }

                    return $existingQueue;
                }
            } elseif ($appointment->status !== 'booked') {
                throw new InvalidArgumentException("Status reservasi saat ini ({$appointment->status}) tidak dapat di-check-in.");
            }

            // Check late tolerance
            $lateTolerance = (int) ClinicSetting::get('late_tolerance_minutes', 15);
            $scheduledTime = Carbon::parse($appointment->appointment_date->format('Y-m-d').' '.$appointment->slot_time);
            $now = Carbon::now();

            if ($now->greaterThan($scheduledTime->copy()->addMinutes($lateTolerance))) {
                $appointment->update(['status' => 'no_show']);
                $lateCheckInMessage = "Waktu check-in telah melebihi batas toleransi keterlambatan ({$lateTolerance} menit dari slot {$appointment->slot_time}). Status diubah menjadi No Show.";

                return null;
            }

            $appointment->update([
                'status' => 'checked_in',
                'checked_in_at' => $now,
            ]);

            // Generate queue code
            $queueCode = $this->generateQueueCode($appointment->service, $today->format('Y-m-d'), false);

            $queueEntry = QueueEntry::create([
                'queue_code' => $queueCode,
                'service_id' => $appointment->service_id,
                'patient_id' => $appointment->patient_id,
                'appointment_id' => $appointment->id,
                'queue_date' => $today->format('Y-m-d'),
                'registration_type' => 'reservation',
                'priority_level' => 3, // Default normal until triage
                'status' => 'waiting_call',
                'current_counter' => null,
            ]);

            $this->logStatusChange($queueEntry, null, 'waiting_call', 'Check-in dari reservasi nomor '.$appointment->appointment_number, $actor);
            $this->estimationService->recalculateEstimations($queueEntry->service_id, $today->format('Y-m-d'));

            return $queueEntry;
        });

        if ($lateCheckInMessage !== null) {
            throw new InvalidArgumentException($lateCheckInMessage);
        }

        return $queueEntry;
    }

    /**
     * Register a walk-in patient.
     */
    public function registerWalkIn(array $data, ?User $actor = null): QueueEntry
    {
        return DB::transaction(function () use ($data, $actor) {
            $service = Service::lockForUpdate()->findOrFail($data['service_id']);
            $patient = Patient::findOrFail($data['patient_id']);
            $today = Carbon::today()->format('Y-m-d');
            $isEmergency = ($data['is_emergency'] ?? false) || (($data['registration_type'] ?? '') === 'emergency');

            // Generate queue code
            $queueCode = $this->generateQueueCode($service, $today, $isEmergency);

            $priority = $isEmergency ? 1 : 3;
            $status = $isEmergency ? 'waiting_call' : 'waiting_triage';

            $queueEntry = QueueEntry::create([
                'queue_code' => $queueCode,
                'service_id' => $service->id,
                'patient_id' => $patient->id,
                'appointment_id' => null,
                'queue_date' => $today,
                'registration_type' => $isEmergency ? 'emergency' : 'walk_in',
                'priority_level' => $priority,
                'status' => $status,
                'current_counter' => null,
            ]);

            $this->logStatusChange(
                $queueEntry,
                null,
                $status,
                $isEmergency ? 'Pendaftaran Darurat (Jalur Respons Segera)' : 'Pendaftaran Pasien Walk-in',
                $actor
            );

            $this->estimationService->recalculateEstimations($service->id, $today);

            return $queueEntry;
        });
    }

    /**
     * Call next queue or specific queue item to a counter.
     */
    public function callQueue(int $queueEntryId, string $counterName, ?User $actor = null): QueueEntry
    {
        return DB::transaction(function () use ($queueEntryId, $counterName, $actor) {
            $queueEntry = QueueEntry::lockForUpdate()->findOrFail($queueEntryId);

            if (! in_array($queueEntry->status, ['waiting_call', 'skipped', 'called'])) {
                throw new InvalidArgumentException("Antrean dengan status '{$queueEntry->status}' tidak dapat dipanggil.");
            }

            $fromStatus = $queueEntry->status;
            $queueEntry->update([
                'status' => 'called',
                'called_at' => Carbon::now(),
                'current_counter' => $counterName,
            ]);

            $this->logStatusChange($queueEntry, $fromStatus, 'called', "Dipanggil ke {$counterName}", $actor);

            return $queueEntry;
        });
    }

    /**
     * Start doctor consultation for a queue item.
     */
    public function startConsultation(int $queueEntryId, ?User $actor = null): QueueEntry
    {
        return DB::transaction(function () use ($queueEntryId, $actor) {
            $queueEntry = QueueEntry::lockForUpdate()->findOrFail($queueEntryId);

            if (! in_array($queueEntry->status, ['called', 'waiting_call'])) {
                throw new InvalidArgumentException('Hanya antrean berstatus Dipanggil atau Siap yang dapat memulai pelayanan.');
            }

            $fromStatus = $queueEntry->status;
            $queueEntry->update([
                'status' => 'in_consultation',
                'consultation_started_at' => Carbon::now(),
            ]);

            $this->logStatusChange($queueEntry, $fromStatus, 'in_consultation', 'Konsultasi poli dimulai', $actor);
            $this->estimationService->recalculateEstimations($queueEntry->service_id, $queueEntry->queue_date->format('Y-m-d'));

            return $queueEntry;
        });
    }

    /**
     * Complete consultation and finish queue.
     */
    public function completeConsultation(int $queueEntryId, array $sessionData, ?User $actor = null): QueueEntry
    {
        return DB::transaction(function () use ($queueEntryId, $sessionData, $actor) {
            $queueEntry = QueueEntry::lockForUpdate()->findOrFail($queueEntryId);

            $now = Carbon::now();
            $startedAt = $queueEntry->consultation_started_at ?? $now->copy()->subMinutes(15);
            $duration = (int) ceil($startedAt->diffInMinutes($now));

            $fromStatus = $queueEntry->status;
            $queueEntry->update([
                'status' => 'completed',
                'completed_at' => $now,
            ]);

            // Save consultation session
            $queueEntry->consultationSession()->create([
                'doctor_id' => $actor?->id ?? $sessionData['doctor_id'],
                'service_id' => $queueEntry->service_id,
                'started_at' => $startedAt,
                'ended_at' => $now,
                'duration_minutes' => $duration,
                'clinical_notes' => $sessionData['clinical_notes'] ?? null,
                'disposition' => $sessionData['disposition'] ?? 'pulang',
            ]);

            if ($queueEntry->appointment_id) {
                $queueEntry->appointment()->update(['status' => 'completed']);
            }

            $this->logStatusChange($queueEntry, $fromStatus, 'completed', 'Pelayanan selesai. Disposisi: '.($sessionData['disposition'] ?? 'pulang'), $actor);
            $this->estimationService->recalculateEstimations($queueEntry->service_id, $queueEntry->queue_date->format('Y-m-d'));

            return $queueEntry;
        });
    }

    /**
     * Skip an unattended queue number.
     */
    public function skipQueue(int $queueEntryId, ?string $reason = null, ?User $actor = null): QueueEntry
    {
        return DB::transaction(function () use ($queueEntryId, $reason, $actor) {
            $queueEntry = QueueEntry::lockForUpdate()->findOrFail($queueEntryId);
            $fromStatus = $queueEntry->status;

            $queueEntry->update(['status' => 'skipped']);
            $this->logStatusChange($queueEntry, $fromStatus, 'skipped', $reason ?? 'Pasien tidak hadir saat dipanggil', $actor);

            $this->estimationService->recalculateEstimations($queueEntry->service_id, $queueEntry->queue_date->format('Y-m-d'));

            return $queueEntry;
        });
    }

    /**
     * Cancel queue entry.
     */
    public function cancelQueue(int $queueEntryId, string $reason, ?User $actor = null): QueueEntry
    {
        return DB::transaction(function () use ($queueEntryId, $reason, $actor) {
            $queueEntry = QueueEntry::lockForUpdate()->findOrFail($queueEntryId);
            $fromStatus = $queueEntry->status;

            $queueEntry->update(['status' => 'cancelled']);
            if ($queueEntry->appointment_id) {
                $queueEntry->appointment()->update(['status' => 'cancelled']);
            }

            $this->logStatusChange($queueEntry, $fromStatus, 'cancelled', $reason, $actor);
            $this->estimationService->recalculateEstimations($queueEntry->service_id, $queueEntry->queue_date->format('Y-m-d'));

            return $queueEntry;
        });
    }

    protected function generateQueueCode(Service $service, string $date, bool $isEmergency): string
    {
        $prefix = $isEmergency ? 'EMG' : strtoupper(substr($service->code, 0, 3));

        $count = QueueEntry::where('service_id', $service->id)
            ->whereDate('queue_date', $date)
            ->where('queue_code', 'LIKE', $prefix.'-%')
            ->count();

        return sprintf('%s-%03d', $prefix, $count + 1);
    }

    public function logStatusChange(QueueEntry $queueEntry, ?string $from, string $to, ?string $reason = null, ?User $actor = null): void
    {
        QueueStatusHistory::create([
            'queue_entry_id' => $queueEntry->id,
            'changed_by' => $actor?->id,
            'from_status' => $from,
            'to_status' => $to,
            'reason' => $reason,
            'created_at' => Carbon::now(),
        ]);
    }
}
