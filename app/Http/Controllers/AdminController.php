<?php

namespace App\Http\Controllers;

use App\Models\ClinicSetting;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Admin dashboard & settings view.
     */
    public function index(): View
    {
        $settings = [
            'opening_time' => ClinicSetting::get('opening_time', '08:00'),
            'closing_time' => ClinicSetting::get('closing_time', '15:00'),
            'default_slot_duration_minutes' => ClinicSetting::get('default_slot_duration_minutes', '20'),
            'max_slot_capacity' => ClinicSetting::get('max_slot_capacity', '4'),
            'late_tolerance_minutes' => ClinicSetting::get('late_tolerance_minutes', '15'),
        ];

        $services = Service::all();
        $users = User::orderBy('name')->get();

        return view('admin.dashboard', [
            'settings' => $settings,
            'services' => $services,
            'users' => $users,
        ]);
    }

    /**
     * Update clinic settings.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'opening_time' => 'required|date_format:H:i',
            'closing_time' => 'required|date_format:H:i|after:opening_time',
            'default_slot_duration_minutes' => 'required|integer|min:5|max:120',
            'max_slot_capacity' => 'required|integer|min:1|max:50',
            'late_tolerance_minutes' => 'required|integer|min:0|max:120',
        ]);

        foreach ($validated as $key => $value) {
            ClinicSetting::set($key, $value);
        }

        return back()->with('success', 'Pengaturan operasional Puskesmas berhasil diperbarui.');
    }

    /**
     * Store new service.
     */
    public function storeService(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:services,code',
            'description' => 'nullable|string|max:255',
            'slot_duration_minutes' => 'required|integer|min:5|max:120',
            'max_capacity_per_slot' => 'required|integer|min:1|max:50',
        ]);

        Service::create([
            ...$validated,
            'code' => strtoupper($validated['code']),
            'is_active' => true,
        ]);

        return back()->with('success', "Layanan poli {$validated['name']} berhasil ditambahkan.");
    }

    /**
     * Update service.
     */
    public function updateService(Request $request, int $serviceId): RedirectResponse
    {
        $service = Service::findOrFail($serviceId);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => ['required', 'string', 'max:10', Rule::unique('services')->ignore($service->id)],
            'description' => 'nullable|string|max:255',
            'slot_duration_minutes' => 'required|integer|min:5|max:120',
            'max_capacity_per_slot' => 'required|integer|min:1|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        $service->update([
            ...$validated,
            'code' => strtoupper($validated['code']),
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', "Layanan poli {$service->name} berhasil diperbarui.");
    }

    /**
     * Store new staff user.
     */
    public function storeUser(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,registrar,health_officer,doctor',
            'phone' => 'nullable|string|max:20',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone' => $validated['phone'],
        ]);

        return back()->with('success', "Akun pengguna {$validated['name']} ({$validated['role']}) berhasil dibuat.");
    }
}
