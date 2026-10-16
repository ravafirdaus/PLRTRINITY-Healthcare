<?php

namespace App\Http\Controllers;

use App\Models\Teleconsultation;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientTeleconsultationController extends Controller
{
    use AuthorizesRequests;

    /**
     * List patient teleconsultations.
     */
    public function index(): View|RedirectResponse
    {
        $patient = auth()->user()->patient;

        if (! $patient) {
            return redirect()->route('dashboard');
        }

        $teleconsultations = $patient->teleconsultations()
            ->with(['service', 'doctor', 'appointment'])
            ->orderByDesc('scheduled_at')
            ->paginate(15);

        return view('patient.telekonsultasi.index', compact('patient', 'teleconsultations'));
    }

    /**
     * Show preparation & informed consent page before joining.
     */
    public function show(Teleconsultation $teleconsultation): View|RedirectResponse
    {
        $this->authorize('view', $teleconsultation);

        $patient = auth()->user()->patient;
        $teleconsultation->load(['service', 'doctor', 'appointment']);

        return view('patient.telekonsultasi.show', compact('patient', 'teleconsultation'));
    }

    /**
     * Store patient informed consent confirmation.
     */
    public function consent(Request $request, Teleconsultation $teleconsultation): RedirectResponse
    {
        $this->authorize('view', $teleconsultation);

        $request->validate([
            'consent' => ['required', 'accepted'],
        ], [
            'consent.accepted' => 'Anda wajib menyetujui ketentuan privasi dan keselamatan sebelum melanjutkan telekonsultasi.',
        ]);

        $teleconsultation->update([
            'patient_consent_given' => true,
            'patient_consent_at' => Carbon::now(),
        ]);

        if ($teleconsultation->canPatientEnterRoom()) {
            return redirect()->route('patient.telekonsultasi.room', $teleconsultation);
        }

        return redirect()->route('patient.telekonsultasi.waiting', $teleconsultation)
            ->with('success', 'Persetujuan telekonsultasi berhasil dicatat. Anda kini berada di ruang tunggu virtual.');
    }

    /**
     * Patient virtual waiting room when doctor is not yet ready.
     */
    public function waiting(Teleconsultation $teleconsultation): View|RedirectResponse
    {
        $this->authorize('accessWaitingRoom', $teleconsultation);

        if (! $teleconsultation->patient_consent_given) {
            return redirect()->route('patient.telekonsultasi.show', $teleconsultation)
                ->with('warning', 'Harap baca dan konfirmasi persetujuan telekonsultasi terlebih dahulu.');
        }

        if ($teleconsultation->canPatientEnterRoom()) {
            return redirect()->route('patient.telekonsultasi.room', $teleconsultation);
        }

        $teleconsultation->load(['service', 'doctor', 'appointment']);

        return view('patient.telekonsultasi.waiting', compact('teleconsultation'));
    }

    /**
     * Live video conference room.
     */
    public function room(Teleconsultation $teleconsultation): View|RedirectResponse
    {
        $this->authorize('view', $teleconsultation);

        if (! $teleconsultation->patient_consent_given) {
            return redirect()->route('patient.telekonsultasi.show', $teleconsultation)
                ->with('warning', 'Harap baca dan konfirmasi persetujuan sebelum masuk ke ruang konsultasi.');
        }

        if ($teleconsultation->isCancelled()) {
            return redirect()->route('patient.dashboard')
                ->withErrors(['error' => 'Sesi telekonsultasi ini telah dibatalkan dan tidak dapat dimasuki.']);
        }

        if ($teleconsultation->isCompleted()) {
            return redirect()->route('patient.dashboard')
                ->with('info', 'Sesi telekonsultasi ini telah selesai.');
        }

        if (! in_array($teleconsultation->status, ['doctor_ready', 'in_progress'])) {
            return redirect()->route('patient.telekonsultasi.waiting', $teleconsultation)
                ->with('info', 'Dokter belum memulai sesi. Silakan menunggu di ruang tunggu virtual.');
        }

        // Record patient join timestamp and transition status to in_progress if doctor is ready
        $updates = [];
        if (! $teleconsultation->patient_joined_at) {
            $updates['patient_joined_at'] = Carbon::now();
        }
        if ($teleconsultation->status === 'doctor_ready') {
            $updates['status'] = 'in_progress';
        }
        if (! empty($updates)) {
            $teleconsultation->update($updates);
        }

        $teleconsultation->load(['service', 'doctor', 'appointment', 'patient']);

        $patientName = auth()->user()->name;
        $patientDisplayName = trim(preg_replace('/\s*\(Pasien\)/i', '', $patientName));
        if (! str_contains(strtolower($patientDisplayName), 'pasien')) {
            $patientDisplayName .= ' (Pasien)';
        }

        $jitsiConfig = [
            'domain' => config('jitsi.domain', 'meet.jit.si'),
            'roomName' => $teleconsultation->room_name,
            'displayName' => $patientDisplayName,
            'subject' => 'Telekonsultasi: '.$teleconsultation->patient->name,
            'role' => 'patient',
            'isDemo' => (bool) config('jitsi.is_demo', true),
            'sessionId' => $teleconsultation->session_id,
        ];

        return view('patient.telekonsultasi.room', compact('teleconsultation', 'jitsiConfig'));
    }

    /**
     * Realtime JSON status polling for virtual waiting room.
     */
    public function status(Teleconsultation $teleconsultation): JsonResponse
    {
        $this->authorize('view', $teleconsultation);

        return response()->json([
            'status' => $teleconsultation->status,
            'status_label' => $teleconsultation->status_label,
            'can_join' => $teleconsultation->canPatientEnterRoom(),
            'doctor_name' => $teleconsultation->doctor?->name,
            'doctor_ready' => in_array($teleconsultation->status, ['doctor_ready', 'in_progress']),
            'is_cancelled' => $teleconsultation->isCancelled(),
            'is_completed' => $teleconsultation->isCompleted(),
        ]);
    }
}
