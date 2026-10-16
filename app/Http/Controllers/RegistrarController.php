<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWalkInRequest;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\QueueEntry;
use App\Models\Service;
use App\Services\QueueManagementService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrarController extends Controller
{
    protected QueueManagementService $queueService;

    public function __construct(QueueManagementService $queueService)
    {
        $this->queueService = $queueService;
    }

    /**
     * Registrar dashboard.
     */
    public function index(Request $request): View
    {
        $today = Carbon::today()->format('Y-m-d');
        $search = $request->input('search');

        // Appointments today
        $appointmentsQuery = Appointment::with(['patient', 'service', 'queueEntry'])
            ->whereDate('appointment_date', $today);

        if ($search) {
            $appointmentsQuery->where(function ($q) use ($search) {
                $q->where('appointment_number', 'ILIKE', "%{$search}%")
                    ->orWhereHas('patient', function ($p) use ($search) {
                        $p->where('nik', 'ILIKE', "%{$search}%")
                            ->orWhere('name', 'ILIKE', "%{$search}%");
                    });
            });
        }

        $statusFilter = $request->input('status');
        if ($statusFilter && $statusFilter !== 'all') {
            $appointmentsQuery->where('status', $statusFilter);
        }

        $appointmentsToday = $appointmentsQuery->orderBy('slot_time')->get();

        // Queue entries today
        $todayQueues = QueueEntry::with(['patient', 'service'])
            ->whereDate('queue_date', $today)
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        $onlineCount = Appointment::whereDate('appointment_date', $today)->count();
        $walkInCount = QueueEntry::whereDate('queue_date', $today)
            ->whereIn('registration_type', ['walk_in', 'emergency'])
            ->count();

        $services = Service::where('is_active', true)->get();

        return view('registrar.dashboard', [
            'appointmentsToday' => $appointmentsToday,
            'todayQueues' => $todayQueues,
            'services' => $services,
            'search' => $search,
            'statusFilter' => $statusFilter,
            'onlineCount' => $onlineCount,
            'walkInCount' => $walkInCount,
            'todayDate' => Carbon::today()->translatedFormat('l, d F Y'),
        ]);
    }

    /**
     * Check in an appointment.
     */
    public function checkIn(Request $request, int $appointmentId): RedirectResponse
    {
        try {
            $queueEntry = $this->queueService->checkInAppointment($appointmentId, $request->user());

            return redirect()->route('registrar.ticket', $queueEntry->id)
                ->with('success', "Check-in berhasil! Nomor antrean pasien: {$queueEntry->queue_code}. Silakan arahkan ke ruang poli.");
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Register a walk-in patient.
     */
    public function storeWalkIn(StoreWalkInRequest $request): RedirectResponse
    {
        try {
            $patient = Patient::firstOrCreate(
                ['nik' => $request->nik],
                [
                    'name' => $request->name,
                    'date_of_birth' => $request->date_of_birth,
                    'gender' => $request->gender,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'bpjs_number' => $request->bpjs_number,
                ]
            );

            if (! $patient->wasRecentlyCreated) {
                $patient->update([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'address' => $request->address ?? $patient->address,
                    'bpjs_number' => $request->bpjs_number ?? $patient->bpjs_number,
                ]);
            }

            $queueEntry = $this->queueService->registerWalkIn([
                'patient_id' => $patient->id,
                'service_id' => (int) $request->service_id,
                'is_emergency' => (bool) $request->is_emergency,
            ], $request->user());

            $msg = $request->is_emergency
                ? "Pasien Gawat Darurat didaftarkan! Nomor antrean: {$queueEntry->queue_code}. JALUR RESPONS SEGERA DIAKTIFKAN!"
                : "Pasien walk-in berhasil didaftarkan! Nomor antrean: {$queueEntry->queue_code}.";

            return redirect()->route('registrar.ticket', $queueEntry->id)->with('success', $msg);
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show printed queue ticket.
     */
    public function ticket(int $queueEntryId): View
    {
        $queueEntry = QueueEntry::with(['patient', 'service'])->findOrFail($queueEntryId);

        return view('registrar.ticket', [
            'queueEntry' => $queueEntry,
        ]);
    }

    /**
     * Cancel a queue entry.
     */
    public function cancel(Request $request, int $queueEntryId): RedirectResponse
    {
        $request->validate(['reason' => 'required|string|max:255']);

        try {
            $this->queueService->cancelQueue($queueEntryId, $request->reason, $request->user());

            return back()->with('success', 'Antrean berhasil dibatalkan.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
