<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\ClinicSetting;
use App\Models\Patient;
use App\Models\Service;
use App\Models\StaffSchedule;
use App\Models\Teleconsultation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SlotAvailabilityService
{
    /**
     * Get available slots for a given service and date.
     *
     * @return array<int, array{time: string, formatted_time: string, capacity: int, booked: int, remaining: int, is_available: bool}>
     */
    public function getAvailableSlots(int $serviceId, string $dateString): array
    {
        $service = Service::findOrFail($serviceId);
        $date = Carbon::parse($dateString);

        $openingTime = ClinicSetting::get('opening_time', '08:00');
        $closingTime = ClinicSetting::get('closing_time', '15:00');
        $slotDuration = $service->slot_duration_minutes ?? (int) ClinicSetting::get('default_slot_duration_minutes', 20);
        $maxCapacity = $service->max_capacity_per_slot ?? (int) ClinicSetting::get('max_slot_capacity', 4);

        $start = Carbon::parse($date->format('Y-m-d').' '.$openingTime);
        $end = Carbon::parse($date->format('Y-m-d').' '.$closingTime);

        // Fetch booked counts per slot time
        $bookedCounts = Appointment::where('service_id', $serviceId)
            ->whereDate('appointment_date', $date->format('Y-m-d'))
            ->whereIn('status', ['booked', 'checked_in'])
            ->select('slot_time', DB::raw('count(*) as total'))
            ->groupBy('slot_time')
            ->pluck('total', 'slot_time')
            ->toArray();

        $slots = [];
        $current = $start->copy();

        while ($current->copy()->addMinutes($slotDuration)->lte($end)) {
            $slotTimeStr = $current->format('H:i:00');
            $formattedTime = $current->format('H:i');

            $booked = $bookedCounts[$slotTimeStr] ?? 0;
            $remaining = max(0, $maxCapacity - $booked);

            $slots[] = [
                'time' => $slotTimeStr,
                'formatted_time' => $formattedTime,
                'capacity' => $maxCapacity,
                'booked' => $booked,
                'remaining' => $remaining,
                'is_available' => $remaining > 0,
            ];

            $current->addMinutes($slotDuration);
        }

        return $slots;
    }

    /**
     * Get available doctors scheduled for a given service and date.
     *
     * @return array<int, array{id: int, doctor_id: int, doctor_name: string, doctor_phone: ?string, service_id: int, service_name: string, day_of_week: int, day_name: string, start_time: string, end_time: string, formatted_schedule: string}>
     */
    public function getAvailableDoctors(int $serviceId, string $dateString): array
    {
        $date = Carbon::parse($dateString);
        $dayOfWeek = (int) $date->dayOfWeekIso; // 1 = Senin, ..., 7 = Minggu

        return StaffSchedule::where('service_id', $serviceId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->with(['user' => fn ($q) => $q->select('id', 'name', 'email', 'phone', 'role'), 'service:id,name,code'])
            ->get()
            ->filter(fn (StaffSchedule $schedule) => $schedule->user && $schedule->user->isDoctor())
            ->map(function (StaffSchedule $schedule) {
                return [
                    'id' => $schedule->id,
                    'doctor_id' => $schedule->user_id,
                    'doctor_name' => $schedule->user->name,
                    'doctor_phone' => $schedule->user->phone,
                    'service_id' => $schedule->service_id,
                    'service_name' => $schedule->service->name,
                    'day_of_week' => $schedule->day_of_week,
                    'day_name' => $schedule->day_name,
                    'start_time' => substr($schedule->start_time, 0, 5),
                    'end_time' => substr($schedule->end_time, 0, 5),
                    'formatted_schedule' => substr($schedule->start_time, 0, 5).' - '.substr($schedule->end_time, 0, 5).' WIB',
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Book an appointment with pessimistic locking to prevent race conditions and overbooking.
     */
    public function bookAppointment(array $data): Appointment
    {
        return DB::transaction(function () use ($data) {
            $service = Service::where('id', $data['service_id'])->lockForUpdate()->firstOrFail();
            $patient = Patient::findOrFail($data['patient_id']);
            $date = Carbon::parse($data['appointment_date'])->format('Y-m-d');
            $slotTime = Carbon::parse($data['slot_time'])->format('H:i:00');

            // 1. Check double booking for the same patient on same service on same date
            $existingAppointment = Appointment::where('patient_id', $patient->id)
                ->where('service_id', $service->id)
                ->whereDate('appointment_date', $date)
                ->whereIn('status', ['booked', 'checked_in'])
                ->first();

            if ($existingAppointment) {
                throw new InvalidArgumentException("Pasien {$patient->name} sudah memiliki reservasi aktif di {$service->name} pada tanggal {$date}.");
            }

            // 2. Count current bookings in this slot (concurrency is protected by service lock above)
            $currentCount = Appointment::where('service_id', $service->id)
                ->whereDate('appointment_date', $date)
                ->where('slot_time', $slotTime)
                ->whereIn('status', ['booked', 'checked_in'])
                ->count();

            $maxCapacity = $service->max_capacity_per_slot ?? (int) ClinicSetting::get('max_slot_capacity', 4);

            if ($currentCount >= $maxCapacity) {
                throw new InvalidArgumentException("Slot waktu {$slotTime} untuk {$service->name} pada tanggal {$date} sudah penuh (kapasitas maksimum: {$maxCapacity}).");
            }

            $consultationType = $data['consultation_type'] ?? 'in_person';
            if ($consultationType === 'video' && ! $service->supports_teleconsultation) {
                throw new InvalidArgumentException("Layanan {$service->name} belum mendukung layanan telekonsultasi video.");
            }

            // Determine doctor from schedule if not explicitly provided
            $dayOfWeek = (int) Carbon::parse($date)->dayOfWeekIso;
            $schedule = StaffSchedule::where('service_id', $service->id)
                ->where('day_of_week', $dayOfWeek)
                ->where('is_active', true)
                ->first();

            $doctorId = ! empty($data['doctor_id']) ? (int) $data['doctor_id'] : $schedule?->user_id;

            // 3. Generate unique appointment number: RES-YYYYMMDD-XXXX
            $datePrefix = 'RES-'.Carbon::parse($date)->format('Ymd').'-';
            $lastAppt = Appointment::where('appointment_number', 'LIKE', $datePrefix.'%')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            $nextSequence = 1;
            if ($lastAppt) {
                $lastNum = (int) substr($lastAppt->appointment_number, strlen($datePrefix));
                $nextSequence = $lastNum + 1;
            }

            $appointmentNumber = $datePrefix.str_pad((string) $nextSequence, 4, '0', STR_PAD_LEFT);

            $appointment = Appointment::create([
                'appointment_number' => $appointmentNumber,
                'patient_id' => $patient->id,
                'doctor_id' => $doctorId,
                'service_id' => $service->id,
                'appointment_date' => $date,
                'slot_time' => $slotTime,
                'consultation_type' => $consultationType,
                'status' => 'booked',
                'notes' => $data['notes'] ?? null,
            ]);

            if ($consultationType === 'video') {
                $scheduledDateTime = Carbon::parse($date.' '.$slotTime);

                $appointment->teleconsultation()->create([
                    'patient_id' => $patient->id,
                    'service_id' => $service->id,
                    'doctor_id' => $doctorId,
                    'session_id' => Teleconsultation::generateSessionId(),
                    'room_name' => Teleconsultation::generateSecureRoomName(),
                    'status' => 'scheduled',
                    'scheduled_at' => $scheduledDateTime,
                ]);
            }

            return $appointment;
        });
    }
}
