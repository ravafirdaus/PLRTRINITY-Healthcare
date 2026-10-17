<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Service;
use App\Services\SlotAvailabilityService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicAppointmentController extends Controller
{
    protected SlotAvailabilityService $slotService;

    public function __construct(SlotAvailabilityService $slotService)
    {
        $this->slotService = $slotService;
    }

    /**
     * Show booking form.
     */
    public function create(): View
    {
        $services = Service::where('is_active', true)->get();

        return view('public.appointments.create', [
            'services' => $services,
            'minDate' => Carbon::today()->format('Y-m-d'),
            'maxDate' => Carbon::today()->addDays(14)->format('Y-m-d'),
        ]);
    }

    /**
     * Check slots available for selected service & date (AJAX endpoint).
     */
    public function checkSlots(Request $request): JsonResponse
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $slots = $this->slotService->getAvailableSlots(
            (int) $request->service_id,
            $request->date
        );

        $doctors = $this->slotService->getAvailableDoctors(
            (int) $request->service_id,
            $request->date
        );

        return response()->json([
            'slots' => $slots,
            'doctors' => $doctors,
        ]);
    }

    /**
     * Store appointment and patient record.
     */
    public function store(StoreAppointmentRequest $request): RedirectResponse
    {
        try {
            // Find or create patient by NIK (data minimization)
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

            // If patient already exists, update non-key details if changed
            if (! $patient->wasRecentlyCreated) {
                $patient->update([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'address' => $request->address ?? $patient->address,
                    'bpjs_number' => $request->bpjs_number ?? $patient->bpjs_number,
                ]);
            }

            $appointment = $this->slotService->bookAppointment([
                'patient_id' => $patient->id,
                'service_id' => (int) $request->service_id,
                'appointment_date' => $request->appointment_date,
                'slot_time' => $request->slot_time,
                'consultation_type' => $request->consultation_type ?? 'in_person',
                'notes' => $request->notes,
            ]);

            return redirect()->route('public.appointments.show', $appointment->appointment_number)
                ->with('success', 'Reservasi berhasil dibuat! Simpan nomor reservasi Anda untuk proses check-in.');
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show booking ticket / details.
     */
    public function show(string $appointmentNumber): View
    {
        $appointment = Appointment::with(['patient', 'service'])
            ->where('appointment_number', $appointmentNumber)
            ->firstOrFail();

        return view('public.appointments.show', [
            'appointment' => $appointment,
        ]);
    }

    /**
     * Search appointment status by NIK or appointment number.
     */
    public function search(Request $request): View
    {
        $query = $request->input('q');
        $appointments = collect();

        if ($query) {
            $appointments = Appointment::with(['patient', 'service'])
                ->where('appointment_number', 'ILIKE', "%{$query}%")
                ->orWhereHas('patient', function ($q) use ($query) {
                    $q->where('nik', 'ILIKE', "%{$query}%")
                        ->orWhere('phone', 'ILIKE', "%{$query}%");
                })
                ->orderByDesc('appointment_date')
                ->limit(10)
                ->get();
        }

        return view('public.appointments.search', [
            'query' => $query,
            'appointments' => $appointments,
        ]);
    }
}
