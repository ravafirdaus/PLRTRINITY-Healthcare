<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientLoginRequest;
use App\Http\Requests\PatientRegisterRequest;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class PatientAuthController extends Controller
{
    public function showRegistrationForm(): View
    {
        return view('patient.auth.register');
    }

    public function register(PatientRegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => 'patient_'.$validated['phone'].'@puskesmasflow.local',
                'password' => $validated['password'],
                'role' => 'patient',
                'phone' => $validated['phone'],
            ]);

            $tier = $validated['patient_tier'] ?? (! empty($validated['bpjs_number']) ? 'subsidi' : 'mandiri');
            $bpjsNumber = ($tier === 'subsidi') ? ($validated['bpjs_number'] ?? null) : null;

            Patient::create([
                'user_id' => $user->id,
                'nik' => $validated['nik'],
                'name' => $validated['name'],
                'date_of_birth' => $validated['date_of_birth'],
                'gender' => $validated['gender'],
                'phone' => $validated['phone'],
                'address' => $validated['address'] ?? null,
                'bpjs_number' => $bpjsNumber,
                'patient_tier' => $tier,
            ]);

            return $user;
        });

        Auth::login($user);

        return redirect()->route('patient.dashboard')
            ->with('success', 'Selamat! Akun Anda berhasil dibuat. Kartu Pasien Digital Anda telah aktif dan siap digunakan.');
    }

    public function showLoginForm(): View
    {
        return view('patient.auth.login');
    }

    public function login(PatientLoginRequest $request): RedirectResponse
    {
        $request->ensureIsNotRateLimited();

        $phone = $request->validated()['phone'];
        $password = $request->validated()['password'];

        // Find patient by normalized phone, then get linked user
        $patient = Patient::where('phone', $phone)->first();

        if (! $patient || ! $patient->user_id) {
            RateLimiter::hit($request->throttleKey());

            return back()->withErrors([
                'phone' => 'Nomor HP tidak terdaftar atau belum memiliki akun. Silakan daftar terlebih dahulu.',
            ])->onlyInput('phone');
        }

        $user = $patient->user;

        if (! $user || ! $user->isPatient()) {
            RateLimiter::hit($request->throttleKey());

            return back()->withErrors([
                'phone' => 'Akun tidak ditemukan. Silakan hubungi petugas.',
            ])->onlyInput('phone');
        }

        if (! Auth::attempt(['email' => $user->email, 'password' => $password])) {
            RateLimiter::hit($request->throttleKey());

            return back()->withErrors([
                'phone' => 'Nomor HP atau password salah.',
            ])->onlyInput('phone');
        }

        RateLimiter::clear($request->throttleKey());
        $request->session()->regenerate();

        return redirect()->intended(route('patient.dashboard'))
            ->with('success', 'Selamat datang kembali! Kartu Pasien Digital Anda aktif dan siap digunakan.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('patient.login')
            ->with('success', 'Anda telah berhasil keluar.');
    }
}
