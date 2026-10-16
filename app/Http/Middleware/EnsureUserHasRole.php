<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // If route is specifically for patients:
        if (in_array('patient', $roles)) {
            if ($user->isPatient() && $user->patient) {
                return $next($request);
            }

            // If staff/admin tries to access patient portal, redirect to their staff dashboard
            if ($user->isStaff()) {
                return redirect()->route('dashboard')
                    ->with('warning', 'Anda sedang login sebagai staf/admin. Portal pasien hanya dapat diakses dengan akun pasien.');
            }

            return redirect()->route('patient.login')
                ->with('error', 'Profil pasien tidak ditemukan. Silakan masuk kembali.');
        }

        // Admin has access to all staff portals
        if ($user->role === 'admin') {
            return $next($request);
        }

        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        if ($user->isPatient()) {
            abort(403, "Akses Ditolak: Anda saat ini sedang login sebagai Pasien ({$user->name}). Fitur dan halaman ini hanya dapat diakses oleh staf medis (Dokter atau Admin). Jika Anda sedang menguji panggilan video dua arah di browser yang sama, buka akun Pasien di Jendela Penyamaran (Incognito Window) agar sesi login Dokter tidak tertimpa.");
        }

        abort(403, 'Akses Ditolak: Akun Anda ('.ucfirst($user->role).') tidak memiliki izin untuk mengakses halaman atau fitur ini.');
    }
}
