<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePatientProfileRequest;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientDashboardController extends Controller
{
    public function dashboard(): View|RedirectResponse
    {
        $patient = auth()->user()->patient;

        if (! $patient) {
            if (auth()->user()->isStaff()) {
                return redirect()->route('dashboard')
                    ->with('warning', 'Anda sedang login sebagai staf/admin. Portal pasien hanya dapat diakses dengan akun pasien.');
            }

            return redirect()->route('patient.login')
                ->with('error', 'Profil pasien tidak ditemukan. Silakan masuk kembali.');
        }

        $activeAppointments = $patient->appointments()
            ->whereIn('status', ['booked', 'checked_in'])
            ->where('appointment_date', '>=', now()->toDateString())
            ->with(['service', 'queueEntry', 'teleconsultation'])
            ->orderBy('appointment_date')
            ->orderBy('slot_time')
            ->get();

        $pastAppointments = $patient->appointments()
            ->whereIn('status', ['completed', 'cancelled', 'no_show'])
            ->with('service')
            ->orderByDesc('appointment_date')
            ->limit(10)
            ->get();

        $activeQueue = $patient->queueEntries()
            ->whereIn('status', ['waiting_triage', 'waiting_call', 'called', 'in_consultation'])
            ->whereDate('queue_date', now()->toDateString())
            ->with('service')
            ->first();

        // Telekonsultasi Online
        $activeTeleconsultations = $patient->teleconsultations()
            ->whereIn('status', ['scheduled', 'waiting_doctor', 'doctor_ready', 'in_progress'])
            ->with(['service', 'doctor', 'appointment'])
            ->orderBy('scheduled_at')
            ->get();

        $pastTeleconsultations = $patient->teleconsultations()
            ->whereIn('status', ['completed', 'cancelled'])
            ->with(['service', 'doctor', 'appointment'])
            ->orderByDesc('scheduled_at')
            ->limit(5)
            ->get();

        $latestReservation = $activeAppointments->first() ?? $pastAppointments->first();
        $todayFormatted = Carbon::today()->translatedFormat('l, d F Y');

        return view('patient.dashboard', compact(
            'patient',
            'activeAppointments',
            'pastAppointments',
            'activeQueue',
            'activeTeleconsultations',
            'pastTeleconsultations',
            'latestReservation',
            'todayFormatted'
        ));
    }

    public function profile(): View|RedirectResponse
    {
        $patient = auth()->user()->patient;

        if (! $patient) {
            return redirect()->route('dashboard');
        }

        return view('patient.profile', compact('patient'));
    }

    public function updateProfile(UpdatePatientProfileRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $patient = auth()->user()->patient;

        $patient->update($validated);

        // Also update the user's name and phone
        auth()->user()->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
        ]);

        return redirect()->route('patient.profile')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    public function showChangePassword(): View
    {
        return view('patient.password');
    }

    public function changePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'current_password.current_password' => 'Password lama salah.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        auth()->user()->update([
            'password' => $request->password,
        ]);

        return redirect()->route('patient.profile')
            ->with('success', 'Password berhasil diubah.');
    }

    /**
     * Dedicated printable view for patient digital card.
     */
    public function printCard(): View|RedirectResponse
    {
        $patient = auth()->user()->patient;

        if (! $patient) {
            return redirect()->route('dashboard');
        }

        return view('patient.card-print', compact('patient'));
    }
}
