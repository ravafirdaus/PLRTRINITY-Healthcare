<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Teleconsultation;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DoctorTeleconsultationController extends Controller
{
    use AuthorizesRequests;

    /**
     * Doctor workspace teleconsultation dashboard.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $today = Carbon::today()->format('Y-m-d');

        // Services accessible by doctor (or all active services if admin)
        if ($user->isAdmin()) {
            $services = Service::where('is_active', true)->where('supports_teleconsultation', true)->get();
        } else {
            $doctorServiceIds = $user->staffSchedules()->pluck('service_id')->unique();
            $services = Service::where('is_active', true)
                ->where('supports_teleconsultation', true)
                ->where(function ($q) use ($doctorServiceIds) {
                    if ($doctorServiceIds->isNotEmpty()) {
                        $q->whereIn('id', $doctorServiceIds);
                    }
                })
                ->get();

            if ($services->isEmpty()) {
                $services = Service::where('is_active', true)->where('supports_teleconsultation', true)->get();
            }
        }

        $selectedServiceId = $request->input('service_id');
        $selectedDate = $request->input('date', $today);

        $query = Teleconsultation::with(['patient', 'service', 'doctor', 'appointment'])
            ->whereDate('scheduled_at', $selectedDate);

        if ($selectedServiceId) {
            $query->where('service_id', $selectedServiceId);
        } elseif ($services->isNotEmpty()) {
            $query->whereIn('service_id', $services->pluck('id'));
        }

        // Doctor specific scope: show assigned to this doctor or unassigned in scheduled services
        if (! $user->isAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->whereNull('doctor_id')
                    ->orWhere('doctor_id', $user->id);
            });
        }

        $teleconsultations = (clone $query)->orderBy('scheduled_at')->get();

        $activeConsultation = $teleconsultations->first(function ($t) {
            return in_array($t->status, ['doctor_ready', 'in_progress']);
        });

        $scheduledCount = $teleconsultations->where('status', 'scheduled')->count();
        $inProgressCount = $teleconsultations->whereIn('status', ['doctor_ready', 'in_progress'])->count();
        $completedCount = $teleconsultations->where('status', 'completed')->count();
        $totalCount = $teleconsultations->count();

        return view('doctor.telekonsultasi.index', compact(
            'services',
            'selectedServiceId',
            'selectedDate',
            'teleconsultations',
            'activeConsultation',
            'scheduledCount',
            'inProgressCount',
            'completedCount',
            'totalCount'
        ));
    }

    /**
     * Show teleconsultation patient clinical details.
     */
    public function show(Teleconsultation $teleconsultation): View
    {
        $this->authorize('view', $teleconsultation);

        $teleconsultation->load(['patient', 'service', 'doctor', 'appointment', 'completedByUser']);

        return view('doctor.telekonsultasi.show', compact('teleconsultation'));
    }

    /**
     * Start teleconsultation session (doctor marks ready and enters).
     */
    public function start(Request $request, Teleconsultation $teleconsultation): RedirectResponse
    {
        $this->authorize('start', $teleconsultation);

        if ($teleconsultation->isCancelled() || $teleconsultation->isCompleted()) {
            return back()->withErrors(['error' => 'Sesi tidak dapat dimulai karena status saat ini: '.$teleconsultation->status_label]);
        }

        DB::transaction(function () use ($teleconsultation) {
            $now = Carbon::now();

            $teleconsultation->update([
                'doctor_id' => auth()->id(),
                'status' => 'doctor_ready',
                'started_at' => $teleconsultation->started_at ?? $now,
                'doctor_joined_at' => $now,
            ]);

            // Update appointment status to checked_in if was booked
            if ($teleconsultation->appointment && $teleconsultation->appointment->status === 'booked') {
                $teleconsultation->appointment->update([
                    'status' => 'checked_in',
                    'checked_in_at' => $now,
                ]);
            }
        });

        return redirect()->route('doctor.telekonsultasi.room', $teleconsultation)
            ->with('success', 'Sesi telekonsultasi berhasil dimulai. Pasien kini dapat bergabung ke ruang video.');
    }

    /**
     * Live video room for doctor.
     */
    public function room(Teleconsultation $teleconsultation): View|RedirectResponse
    {
        $this->authorize('joinAsDoctor', $teleconsultation);

        if ($teleconsultation->isCancelled()) {
            return redirect()->route('doctor.telekonsultasi.index')
                ->withErrors(['error' => 'Sesi telekonsultasi telah dibatalkan.']);
        }

        if ($teleconsultation->isCompleted()) {
            return redirect()->route('doctor.telekonsultasi.show', $teleconsultation)
                ->with('info', 'Sesi telekonsultasi ini telah selesai.');
        }

        // Record doctor joined timestamp
        if (! $teleconsultation->doctor_joined_at) {
            $teleconsultation->update(['doctor_joined_at' => Carbon::now()]);
        }

        $teleconsultation->load(['patient', 'service', 'doctor', 'appointment']);

        $doctorName = auth()->user()->name;
        if (! preg_match('/(dr\.|dokter)/i', $doctorName)) {
            $doctorDisplayName = 'dr. '.$doctorName;
        } else {
            $doctorDisplayName = trim(preg_replace('/\s*\(Dokter\s*Pemeriksa\)/i', '', $doctorName));
        }

        $jitsiConfig = [
            'domain' => config('jitsi.domain', 'meet.jit.si'),
            'roomName' => $teleconsultation->room_name,
            'displayName' => $doctorDisplayName,
            'subject' => 'Telekonsultasi: '.$teleconsultation->patient->name,
            'role' => 'doctor',
            'isDemo' => (bool) config('jitsi.is_demo', true),
            'sessionId' => $teleconsultation->session_id,
        ];

        return view('doctor.telekonsultasi.room', compact('teleconsultation', 'jitsiConfig'));
    }

    /**
     * Complete teleconsultation and write clinical summary.
     */
    public function complete(Request $request, Teleconsultation $teleconsultation): RedirectResponse
    {
        $this->authorize('complete', $teleconsultation);

        $request->validate([
            'clinical_notes' => ['nullable', 'string', 'max:5000'],
            'disposition' => ['required', 'string', 'in:pulang,rujuk_lanjut,resep_obat,kontrol_ulang'],
        ], [
            'disposition.required' => 'Pilih tindak lanjut / disposisi medis pasien.',
            'disposition.in' => 'Pilihan tindak lanjut tidak valid.',
            'clinical_notes.max' => 'Ringkasan klinis maksimal 5000 karakter.',
        ]);

        DB::transaction(function () use ($request, $teleconsultation) {
            $now = Carbon::now();
            $startedAt = $teleconsultation->started_at ?? $now->copy()->subMinutes(15);
            $duration = (int) max(1, ceil($startedAt->diffInMinutes($now)));

            $teleconsultation->update([
                'status' => 'completed',
                'ended_at' => $now,
                'duration_minutes' => $duration,
                'completed_by' => auth()->id(),
                'clinical_notes' => $request->clinical_notes,
                'disposition' => $request->disposition,
            ]);

            if ($teleconsultation->appointment) {
                $teleconsultation->appointment->update(['status' => 'completed']);
            }
        });

        return redirect()->route('doctor.telekonsultasi.show', $teleconsultation)
            ->with('success', 'Sesi telekonsultasi berhasil diselesaikan dan ringkasan medis telah disimpan.');
    }
}
