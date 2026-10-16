<?php

namespace App\Services;

use App\Models\QueueEntry;
use App\Models\TriageAssessment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TriageService
{
    protected EstimationService $estimationService;

    public function __construct(EstimationService $estimationService)
    {
        $this->estimationService = $estimationService;
    }

    /**
     * Submit triage assessment by authorized healthcare personnel.
     */
    public function assessTriage(int $queueEntryId, array $data, User $assessor): TriageAssessment
    {
        return DB::transaction(function () use ($queueEntryId, $data, $assessor) {
            $queueEntry = QueueEntry::lockForUpdate()->findOrFail($queueEntryId);

            if (! in_array($queueEntry->status, ['waiting_triage', 'waiting_call'])) {
                throw new InvalidArgumentException("Antrean dengan status '{$queueEntry->status}' tidak dapat dilakukan penilaian triase.");
            }

            $category = strtolower($data['category']);
            if (! in_array($category, ['merah', 'kuning', 'hijau', 'hitam'])) {
                throw new InvalidArgumentException("Kategori triase tidak valid: {$category}.");
            }

            // Map triage category to operational priority level
            // 1: Emergency (Merah)
            // 2: Urgent (Kuning)
            // 3: Normal (Hijau)
            $priorityLevel = match ($category) {
                'merah' => 1,
                'kuning' => 2,
                'hijau' => 3,
                'hitam' => 9,
            };

            $isEmergency = ($category === 'merah');

            $assessment = TriageAssessment::updateOrCreate(
                ['queue_entry_id' => $queueEntry->id],
                [
                    'assessed_by' => $assessor->id,
                    'category' => $category,
                    'systolic_bp' => $data['systolic_bp'] ?? null,
                    'diastolic_bp' => $data['diastolic_bp'] ?? null,
                    'heart_rate' => $data['heart_rate'] ?? null,
                    'respiratory_rate' => $data['respiratory_rate'] ?? null,
                    'temperature' => $data['temperature'] ?? null,
                    'oxygen_saturation' => $data['oxygen_saturation'] ?? null,
                    'consciousness_level' => $data['consciousness_level'] ?? 'compos_mentis',
                    'chief_complaint' => $data['chief_complaint'],
                    'observations' => $data['observations'] ?? null,
                    'is_immediate_response_triggered' => $isEmergency,
                    'assessed_at' => Carbon::now(),
                ]
            );

            $fromStatus = $queueEntry->status;
            $newStatus = ($category === 'hitam') ? 'cancelled' : 'waiting_call';

            $queueEntry->update([
                'priority_level' => $priorityLevel,
                'status' => $newStatus,
            ]);

            $reason = 'Penilaian Triase: Kategori '.strtoupper($category).' oleh '.$assessor->name;
            if ($isEmergency) {
                $reason .= ' [JALUR RESPONS SEGERA DIAKTIFKAN]';
            }

            // Log status history
            $queueEntry->statusHistories()->create([
                'changed_by' => $assessor->id,
                'from_status' => $fromStatus,
                'to_status' => $newStatus,
                'reason' => $reason,
                'created_at' => Carbon::now(),
            ]);

            // Recalculate estimated call times
            $this->estimationService->recalculateEstimations($queueEntry->service_id, $queueEntry->queue_date->format('Y-m-d'));

            return $assessment;
        });
    }
}
