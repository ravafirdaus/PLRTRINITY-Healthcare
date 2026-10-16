<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTriageRequest;
use App\Models\QueueEntry;
use App\Models\TriageAssessment;
use App\Services\TriageService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TriageController extends Controller
{
    protected TriageService $triageService;

    public function __construct(TriageService $triageService)
    {
        $this->triageService = $triageService;
    }

    /**
     * Triage workspace dashboard.
     */
    public function index(): View
    {
        $today = Carbon::today()->format('Y-m-d');

        // Patients waiting for triage assessment
        $waitingTriage = QueueEntry::with(['patient', 'service'])
            ->whereDate('queue_date', $today)
            ->where('status', 'waiting_triage')
            ->orderBy('id', 'asc')
            ->get();

        // Already assessed today
        $assessedToday = TriageAssessment::with(['queueEntry.patient', 'queueEntry.service', 'assessedBy'])
            ->whereHas('queueEntry', function ($q) use ($today) {
                $q->whereDate('queue_date', $today);
            })
            ->orderByDesc('assessed_at')
            ->limit(20)
            ->get();

        return view('triage.index', [
            'waitingTriage' => $waitingTriage,
            'assessedToday' => $assessedToday,
            'todayDate' => Carbon::today()->translatedFormat('l, d F Y'),
        ]);
    }

    /**
     * Show triage form for a specific queue entry.
     */
    public function create(int $queueEntryId): View
    {
        $queueEntry = QueueEntry::with(['patient', 'service', 'triageAssessment'])->findOrFail($queueEntryId);

        return view('triage.form', [
            'queueEntry' => $queueEntry,
        ]);
    }

    /**
     * Store or update triage assessment.
     */
    public function store(StoreTriageRequest $request, int $queueEntryId): RedirectResponse
    {
        try {
            $assessment = $this->triageService->assessTriage(
                $queueEntryId,
                $request->validated(),
                $request->user()
            );

            $badge = strtoupper($assessment->category);
            $msg = "Penilaian triase berhasil dicatat [Kategori {$badge}]. Antrean dialihkan ke antrean poli.";

            if ($assessment->is_immediate_response_triggered) {
                $msg .= ' PERINGATAN: Pasien Darurat (Merah) diaktifkan prioritas tertinggi!';
            }

            return redirect()->route('triage.index')->with('success', $msg);
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
