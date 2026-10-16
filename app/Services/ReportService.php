<?php

namespace App\Services;

use App\Models\ConsultationSession;
use App\Models\QueueEntry;
use App\Models\Service;
use App\Models\TriageAssessment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Get operational statistics for a given date range.
     *
     * @return array<string, mixed>
     */
    public function getSummaryReport(?string $startDate = null, ?string $endDate = null): array
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::today()->startOfDay();
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::today()->endOfDay();

        $queuesQuery = QueueEntry::whereBetween('queue_date', [$start->format('Y-m-d'), $end->format('Y-m-d')]);

        $totalPatients = (clone $queuesQuery)->count();
        $completedPatients = (clone $queuesQuery)->where('status', 'completed')->count();
        $waitingPatients = (clone $queuesQuery)->whereIn('status', ['waiting_triage', 'waiting_call', 'called', 'in_consultation'])->count();
        $skippedPatients = (clone $queuesQuery)->where('status', 'skipped')->count();
        $cancelledPatients = (clone $queuesQuery)->where('status', 'cancelled')->count();

        // Breakdown by registration type
        $byRegType = (clone $queuesQuery)
            ->select('registration_type', DB::raw('count(*) as count'))
            ->groupBy('registration_type')
            ->pluck('count', 'registration_type')
            ->toArray();

        // Triage distribution
        $triageDistribution = TriageAssessment::whereBetween('created_at', [$start, $end])
            ->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        // Service breakdown
        $serviceBreakdown = Service::withCount(['queueEntries' => function ($query) use ($start, $end) {
            $query->whereBetween('queue_date', [$start->format('Y-m-d'), $end->format('Y-m-d')]);
        }])->get();

        // Average consultation duration
        $avgConsultationDuration = ConsultationSession::whereBetween('started_at', [$start, $end])
            ->whereNotNull('duration_minutes')
            ->avg('duration_minutes');

        // Recent completed sessions
        $recentSessions = ConsultationSession::with(['queueEntry.patient', 'doctor', 'service'])
            ->whereBetween('started_at', [$start, $end])
            ->orderByDesc('ended_at')
            ->limit(10)
            ->get();

        return [
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'total_patients' => $totalPatients,
            'completed_patients' => $completedPatients,
            'waiting_patients' => $waitingPatients,
            'skipped_patients' => $skippedPatients,
            'cancelled_patients' => $cancelledPatients,
            'by_reg_type' => [
                'reservation' => $byRegType['reservation'] ?? 0,
                'walk_in' => $byRegType['walk_in'] ?? 0,
                'emergency' => $byRegType['emergency'] ?? 0,
            ],
            'triage' => [
                'merah' => $triageDistribution['merah'] ?? 0,
                'kuning' => $triageDistribution['kuning'] ?? 0,
                'hijau' => $triageDistribution['hijau'] ?? 0,
                'hitam' => $triageDistribution['hitam'] ?? 0,
            ],
            'services' => $serviceBreakdown,
            'avg_duration_minutes' => $avgConsultationDuration ? round($avgConsultationDuration, 1) : 0,
            'recent_sessions' => $recentSessions,
        ];
    }
}
