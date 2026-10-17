<?php

namespace App\Http\Controllers;

use App\Models\QueueEntry;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicQueueController extends Controller
{
    /**
     * Public queue display view.
     */
    public function index(): View
    {
        $today = Carbon::today()->format('Y-m-d');
        $services = Service::where('is_active', true)->get();

        return view('public.queue', [
            'services' => $services,
            'todayFormatted' => Carbon::today()->translatedFormat('l, d F Y'),
        ]);
    }

    /**
     * JSON API for periodic polling without page reload.
     */
    public function statusData(Request $request): JsonResponse
    {
        $today = Carbon::today()->format('Y-m-d');

        // Currently called or in consultation per service (PUBLIC SAFE: NO SENSITIVE DATA)
        $activeCalls = QueueEntry::with(['service', 'patient'])
            ->whereDate('queue_date', $today)
            ->whereIn('status', ['called', 'in_consultation'])
            ->orderByDesc('called_at')
            ->get()
            ->map(function ($entry) {
                $counterName = $entry->current_counter ?: ('Ruang '.$entry->service->name);

                return [
                    'id' => $entry->id,
                    'queue_code' => $entry->queue_code,
                    'patient_name' => $entry->patient?->name ?? 'Pasien',
                    'service_name' => $entry->service->name,
                    'counter' => $counterName,
                    'counter_name' => $counterName,
                    'status' => $entry->status,
                    'status_label' => $entry->status_label,
                    'priority_level' => $entry->priority_level,
                    'called_at' => $entry->called_at ? $entry->called_at->format('H:i:s') : null,
                ];
            });

        // Next waiting queues (Waiting call)
        $waitingQueues = QueueEntry::with(['service', 'patient'])
            ->whereDate('queue_date', $today)
            ->where('status', 'waiting_call')
            ->orderBy('priority_level', 'asc')
            ->orderBy('created_at', 'asc')
            ->limit(15)
            ->get()
            ->map(function ($entry) {
                return [
                    'queue_code' => $entry->queue_code,
                    'patient_name' => $entry->patient?->name ?? 'Pasien',
                    'service_name' => $entry->service->name,
                    'priority_level' => $entry->priority_level,
                    'priority_label' => $entry->priority_label,
                    'estimated_call' => $entry->estimated_call_time ? $entry->estimated_call_time->format('H:i') : null,
                ];
            });

        // Summary counts
        $totalWaiting = QueueEntry::whereDate('queue_date', $today)->whereIn('status', ['waiting_triage', 'waiting_call'])->count();
        $totalCompleted = QueueEntry::whereDate('queue_date', $today)->where('status', 'completed')->count();
        $totalEmergency = QueueEntry::whereDate('queue_date', $today)->where('priority_level', 1)->count();

        return response()->json([
            'updated_at' => Carbon::now()->format('H:i:s'),
            'active_calls' => $activeCalls,
            'waiting_queues' => $waitingQueues,
            'counts' => [
                'waiting' => $totalWaiting,
                'completed' => $totalCompleted,
                'emergency' => $totalEmergency,
            ],
        ]);
    }
}
