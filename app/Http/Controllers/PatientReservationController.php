<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientReservationRequest;
use App\Models\Appointment;
use App\Models\Service;
use App\Services\SlotAvailabilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PatientReservationController extends Controller
{
    public function __construct(
        private SlotAvailabilityService $slotService
    ) {}

    public function create(): View|RedirectResponse
    {
        $patient = auth()->user()->patient;

        if (! $patient) {
            return redirect()->route('dashboard');
        }

        $services = Service::where('is_active', true)->get();

        return view('patient.reservasi.create', compact('patient', 'services'));
    }

    public function checkSlots(Request $request): JsonResponse
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $slots = $this->slotService->getAvailableSlots(
            $request->service_id,
            $request->date
        );

        return response()->json(['slots' => $slots]);
    }

    public function store(PatientReservationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $patient = auth()->user()->patient;

        if (! $patient) {
            return redirect()->route('dashboard');
        }

        try {
            $appointment = $this->slotService->bookAppointment([
                'patient_id' => $patient->id,
                'service_id' => (int) $validated['service_id'],
                'appointment_date' => $validated['appointment_date'],
                'slot_time' => $validated['slot_time'],
                'consultation_type' => $validated['consultation_type'] ?? 'in_person',
                'notes' => $validated['notes'] ?? null,
            ]);

            return redirect()->route('patient.reservasi.show', $appointment)
                ->with('success', 'Reservasi berhasil dibuat! Simpan kode reservasi Anda.');
        } catch (\InvalidArgumentException $e) {
            $key = str_contains($e->getMessage(), 'telekonsultasi') ? 'consultation_type' : 'slot_time';

            return back()->withErrors([$key => $e->getMessage()])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['service_id' => 'Gagal memproses reservasi: '.$e->getMessage()])->withInput();
        }
    }

    public function show(Appointment $appointment): View|RedirectResponse
    {
        $patient = auth()->user()->patient;

        if (! $patient) {
            return redirect()->route('dashboard');
        }

        if ($appointment->patient_id !== $patient->id) {
            abort(403, 'Anda tidak memiliki akses ke reservasi ini.');
        }

        $appointment->load(['service', 'patient', 'queueEntry', 'teleconsultation.doctor']);

        return view('patient.reservasi.show', compact('appointment', 'patient'));
    }

    public function history(): View|RedirectResponse
    {
        $patient = auth()->user()->patient;

        if (! $patient) {
            return redirect()->route('dashboard');
        }

        $appointments = $patient->appointments()
            ->with(['service', 'teleconsultation'])
            ->orderByDesc('appointment_date')
            ->orderByDesc('slot_time')
            ->paginate(15);

        return view('patient.reservasi.history', compact('appointments', 'patient'));
    }

    public function cancel(Appointment $appointment): RedirectResponse
    {
        $patient = auth()->user()->patient;

        if (! $patient) {
            return redirect()->route('dashboard');
        }

        if ($appointment->patient_id !== $patient->id) {
            abort(403);
        }

        if (! in_array($appointment->status, ['booked'])) {
            return back()->withErrors(['status' => 'Reservasi tidak dapat dibatalkan karena sudah diproses atau dibatalkan sebelumnya.']);
        }

        DB::transaction(function () use ($appointment) {
            $appointment->update(['status' => 'cancelled']);
            if ($appointment->teleconsultation) {
                $appointment->teleconsultation->update(['status' => 'cancelled']);
            }
        });

        return redirect()->route('patient.dashboard')
            ->with('success', 'Reservasi berhasil dibatalkan.');
    }
}
