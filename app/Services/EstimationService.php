<?php

namespace App\Services;

use App\Models\ClinicSetting;
use App\Models\QueueEntry;
use App\Models\Service;
use Carbon\Carbon;

class EstimationService
{
    /**
     * Recalculate estimated call times for all waiting patients in a service on a given date.
     */
    public function recalculateEstimations(int $serviceId, string $dateString): void
    {
        $service = Service::find($serviceId);
        if (! $service) {
            return;
        }

        $slotDuration = $service->slot_duration_minutes ?? (int) ClinicSetting::get('default_slot_duration_minutes', 20);
        $now = Carbon::now();

        // Check if there is currently a patient in consultation
        $activeConsultation = QueueEntry::where('service_id', $serviceId)
            ->whereDate('queue_date', $dateString)
            ->where('status', 'in_consultation')
            ->first();

        $baseTime = $now->copy();

        if ($activeConsultation && $activeConsultation->consultation_started_at) {
            $expectedFinish = $activeConsultation->consultation_started_at->copy()->addMinutes($slotDuration);
            if ($expectedFinish->gt($now)) {
                $baseTime = $expectedFinish;
            }
        }

        // Get waiting queues ordered by priority (1=Emergency, 2=Urgent, 3=Normal) then created_at asc
        $waitingQueues = QueueEntry::where('service_id', $serviceId)
            ->whereDate('queue_date', $dateString)
            ->whereIn('status', ['waiting_call', 'called'])
            ->orderBy('priority_level', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        $runningTime = $baseTime->copy();

        foreach ($waitingQueues as $queue) {
            // Emergency (level 1) gets immediate attention
            if ($queue->priority_level === 1) {
                $queue->update([
                    'estimated_call_time' => $now->copy(),
                ]);
            } else {
                $queue->update([
                    'estimated_call_time' => $runningTime->copy(),
                ]);
                $runningTime->addMinutes($slotDuration);
            }
        }
    }

    /**
     * Get estimated wait time in minutes for a specific queue entry.
     */
    public function getEstimatedWaitMinutes(QueueEntry $queueEntry): int
    {
        if ($queueEntry->status === 'in_consultation' || $queueEntry->status === 'completed') {
            return 0;
        }

        if (! $queueEntry->estimated_call_time) {
            return 0;
        }

        $now = Carbon::now();
        if ($queueEntry->estimated_call_time->lte($now)) {
            return 0;
        }

        return (int) ceil($now->diffInMinutes($queueEntry->estimated_call_time));
    }
}
