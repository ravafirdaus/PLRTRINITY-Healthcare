<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConsultationRequest;
use App\Models\QueueEntry;
use App\Models\Service;
use App\Services\EstimationService;
use App\Services\QueueManagementService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DoctorController extends Controller
{
    protected QueueManagementService $queueService;

    protected EstimationService $estimationService;

    public function __construct(QueueManagementService $queueService, EstimationService $estimationService)
    {
        $this->queueService = $queueService;
        $this->estimationService = $estimationService;
    }

    /**
     * Doctor workspace dashboard.
     */
    public function index(Request $request): View
    {
        $today = Carbon::today()->format('Y-m-d');
        $services = Service::where('is_active', true)->get();

        // Active service selected by doctor
        $selectedServiceId = $request->input('service_id', $services->first()?->id);
        $activeService = Service::find($selectedServiceId) ?? $services->first();

        // Ensure any reservation queue entries for today have the operational status 'waiting_call'
        $staleReservationQueues = QueueEntry::whereDate('queue_date', $today)
            ->where('status', 'waiting_triage')
            ->where(function ($q) {
                $q->whereNotNull('appointment_id')
                    ->orWhere('registration_type', 'reservation');
            })
            ->get();

        if ($staleReservationQueues->isNotEmpty()) {
            foreach ($staleReservationQueues as $staleQueue) {
                $staleQueue->update(['status' => 'waiting_call']);
                $this->estimationService->recalculateEstimations($staleQueue->service_id, $today);
            }
        }

        // Current patient in consultation in this service
        $currentPatient = QueueEntry::with(['patient', 'triageAssessment', 'service'])
            ->where('service_id', $activeService?->id)
            ->whereDate('queue_date', $today)
            ->whereIn('status', ['called', 'in_consultation'])
            ->first();

        // Waiting queue ordered by priority (1=Emergency, 2=Urgent, 3=Normal) then created_at asc
        $waitingQueues = QueueEntry::with(['patient', 'triageAssessment', 'service'])
            ->where('service_id', $activeService?->id)
            ->whereDate('queue_date', $today)
            ->where('status', 'waiting_call')
            ->orderBy('priority_level', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        // Skipped patients
        $skippedQueues = QueueEntry::with(['patient', 'triageAssessment'])
            ->where('service_id', $activeService?->id)
            ->whereDate('queue_date', $today)
            ->where('status', 'skipped')
            ->get();

        // Completed consultations today in this service
        $completedToday = QueueEntry::with(['patient', 'consultationSession', 'triageAssessment'])
            ->where('service_id', $activeService?->id)
            ->whereDate('queue_date', $today)
            ->where('status', 'completed')
            ->orderByDesc('completed_at')
            ->limit(10)
            ->get();

        $totalQueueToday = QueueEntry::where('service_id', $activeService?->id)
            ->whereDate('queue_date', $today)
            ->count();
        $waitingCount = $waitingQueues->count();
        $inProgressCount = $currentPatient ? 1 : 0;
        $completedCount = QueueEntry::where('service_id', $activeService?->id)
            ->whereDate('queue_date', $today)
            ->where('status', 'completed')
            ->count();

        return view('doctor.dashboard', [
            'services' => $services,
            'activeService' => $activeService,
            'currentPatient' => $currentPatient,
            'waitingQueues' => $waitingQueues,
            'skippedQueues' => $skippedQueues,
            'completedToday' => $completedToday,
            'totalQueueToday' => $totalQueueToday,
            'waitingCount' => $waitingCount,
            'inProgressCount' => $inProgressCount,
            'completedCount' => $completedCount,
            'todayDate' => Carbon::today()->translatedFormat('l, d F Y'),
        ]);
    }

    /**
     * Call patient to consultation room.
     */
    public function call(Request $request, int $queueEntryId): RedirectResponse
    {
        try {
            $queueEntry = QueueEntry::findOrFail($queueEntryId);
            $counterName = $request->input('counter', 'Ruang '.$queueEntry->service->name);

            $this->queueService->callQueue($queueEntryId, $counterName, $request->user());

            return back()->with('success', "Pasien dengan nomor antrean {$queueEntry->queue_code} telah dipanggil.");
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Start consultation.
     */
    public function start(Request $request, int $queueEntryId): RedirectResponse
    {
        try {
            $queueEntry = $this->queueService->startConsultation($queueEntryId, $request->user());

            return back()->with('success', "Pelayanan konsultasi untuk antrean {$queueEntry->queue_code} dimulai.");
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Complete consultation and save notes.
     */
    public function complete(StoreConsultationRequest $request, int $queueEntryId): RedirectResponse
    {
        try {
            $queueEntry = $this->queueService->completeConsultation(
                $queueEntryId,
                [
                    'doctor_id' => $request->user()->id,
                    'disposition' => $request->disposition,
                    'clinical_notes' => $request->clinical_notes,
                ],
                $request->user()
            );

            return back()->with('success', "Pelayanan antrean {$queueEntry->queue_code} selesai. Status diperbarui.");
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Skip patient if not responding.
     */
    public function skip(Request $request, int $queueEntryId): RedirectResponse
    {
        try {
            $this->queueService->skipQueue($queueEntryId, $request->input('reason', 'Tidak hadir setelah dipanggil'), $request->user());

            return back()->with('success', 'Antrean dilewati.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
