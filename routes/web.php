<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\DoctorTeleconsultationController;
use App\Http\Controllers\PatientAuthController;
use App\Http\Controllers\PatientDashboardController;
use App\Http\Controllers\PatientReservationController;
use App\Http\Controllers\PatientTeleconsultationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicAppointmentController;
use App\Http\Controllers\PublicQueueController;
use App\Http\Controllers\RegistrarController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TriageController;
use App\Models\QueueEntry;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Public Routes (Pasien & Publik)
|--------------------------------------------------------------------------
*/
Route::view('/demo', 'demo')->name('demo');

Route::get('/', function () {
    $services = collect();
    $todayQueueCount = 0;
    $waitingCount = 0;
    $completedCount = 0;
    $callingQueue = null;
    $nextQueues = collect();

    try {
        if (Schema::hasTable('services')) {
            $services = Service::where('is_active', true)->get();
        }

        if (Schema::hasTable('queue_entries')) {
            $today = Carbon::today();

            $todayQueueCount = QueueEntry::whereDate('queue_date', $today)->count();
            $waitingCount = QueueEntry::whereDate('queue_date', $today)
                ->whereIn('status', ['waiting_triage', 'waiting_call'])
                ->count();
            $completedCount = QueueEntry::whereDate('queue_date', $today)
                ->where('status', 'completed')
                ->count();

            $callingQueue = QueueEntry::whereDate('queue_date', $today)
                ->whereIn('status', ['called', 'in_consultation'])
                ->with('service')
                ->latest('updated_at')
                ->first();

            $nextQueues = QueueEntry::whereDate('queue_date', $today)
                ->whereIn('status', ['waiting_call', 'waiting_triage'])
                ->orderBy('priority_level')
                ->orderBy('id')
                ->with('service')
                ->take(4)
                ->get();
        }
    } catch (Throwable $e) {
        // Fallback gracefully if database table is not ready
    }

    return view('welcome', compact(
        'services',
        'todayQueueCount',
        'waitingCount',
        'completedCount',
        'callingQueue',
        'nextQueues'
    ));
})->name('home');

// Antrean Publik
Route::get('/antrean', [PublicQueueController::class, 'index'])->name('public.queue');
Route::get('/antrean/api-status', [PublicQueueController::class, 'statusData'])->name('public.queue.data');

// Reservasi Pasien Mandiri (Legacy / Direct)
Route::get('/reservasi', [PublicAppointmentController::class, 'create'])->name('public.appointments.create');
Route::post('/reservasi', [PublicAppointmentController::class, 'store'])->name('public.appointments.store');
Route::get('/reservasi/cek-slot', [PublicAppointmentController::class, 'checkSlots'])->name('public.appointments.check_slots');
Route::get('/reservasi/cek-status', [PublicAppointmentController::class, 'search'])->name('public.appointments.search');
Route::get('/reservasi/{appointmentNumber}', [PublicAppointmentController::class, 'show'])->name('public.appointments.show');

/*
|--------------------------------------------------------------------------
| Patient Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/pasien/daftar', [PatientAuthController::class, 'showRegistrationForm'])->name('patient.register');
    Route::post('/pasien/daftar', [PatientAuthController::class, 'register'])->name('patient.register.store');
    Route::get('/pasien/masuk', [PatientAuthController::class, 'showLoginForm'])->name('patient.login');
    Route::post('/pasien/masuk', [PatientAuthController::class, 'login'])->name('patient.login.store');
});
Route::post('/pasien/keluar', [PatientAuthController::class, 'logout'])->name('patient.logout');

/*
|--------------------------------------------------------------------------
| Authenticated Patient Portal Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:patient'])->prefix('pasien')->name('patient.')->group(function () {
    Route::get('/dashboard', [PatientDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/kartu/cetak', [PatientDashboardController::class, 'printCard'])->name('card.print');
    Route::get('/profil', [PatientDashboardController::class, 'profile'])->name('profile');
    Route::put('/profil', [PatientDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::get('/profil/password', [PatientDashboardController::class, 'showChangePassword'])->name('password');
    Route::put('/profil/password', [PatientDashboardController::class, 'changePassword'])->name('password.update');

    Route::prefix('reservasi')->name('reservasi.')->group(function () {
        Route::get('/buat', [PatientReservationController::class, 'create'])->name('create');
        Route::get('/slots', [PatientReservationController::class, 'checkSlots'])->name('slots');
        Route::post('/', [PatientReservationController::class, 'store'])->name('store');
        Route::get('/riwayat', [PatientReservationController::class, 'history'])->name('history');
        Route::get('/{appointment}', [PatientReservationController::class, 'show'])->name('show');
        Route::patch('/{appointment}/batal', [PatientReservationController::class, 'cancel'])->name('cancel');
    });

    Route::prefix('telekonsultasi')->name('telekonsultasi.')->group(function () {
        Route::get('/', [PatientTeleconsultationController::class, 'index'])->name('index');
        Route::get('/{teleconsultation}', [PatientTeleconsultationController::class, 'show'])->name('show');
        Route::post('/{teleconsultation}/persetujuan', [PatientTeleconsultationController::class, 'consent'])->name('consent');
        Route::get('/{teleconsultation}/ruang-tunggu', [PatientTeleconsultationController::class, 'waiting'])->name('waiting');
        Route::get('/{teleconsultation}/ruang-video', [PatientTeleconsultationController::class, 'room'])->name('room');
        Route::get('/{teleconsultation}/status', [PatientTeleconsultationController::class, 'status'])->name('status');
    });
});

/*
|--------------------------------------------------------------------------
| Authenticated Staff Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Dynamic dashboard redirection based on role
    Route::get('/dashboard', function () {
        $user = auth()->user();

        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'doctor' => redirect()->route('doctor.dashboard'),
            'health_officer' => redirect()->route('triage.index'),
            'registrar' => redirect()->route('registrar.dashboard'),
            'patient' => redirect()->route('patient.dashboard'),
            default => redirect()->route('public.queue'),
        };
    })->name('dashboard');

    // Profile settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Laporan Operasional (Dapat diakses oleh seluruh staf medis/admin)
    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');

    // Role: Petugas Pendaftaran (Registrar) & Admin
    Route::middleware(['role:registrar,admin'])->prefix('pendaftaran')->name('registrar.')->group(function () {
        Route::get('/', [RegistrarController::class, 'index'])->name('dashboard');
        Route::post('/check-in/{appointmentId}', [RegistrarController::class, 'checkIn'])->name('check_in');
        Route::post('/walk-in', [RegistrarController::class, 'storeWalkIn'])->name('walk_in');
        Route::get('/tiket/{queueEntryId}', [RegistrarController::class, 'ticket'])->name('ticket');
        Route::post('/batal/{queueEntryId}', [RegistrarController::class, 'cancel'])->name('cancel');
    });

    // Role: Petugas Kesehatan (Health Officer / Triase) & Admin
    Route::middleware(['role:health_officer,admin'])->prefix('triase')->name('triage.')->group(function () {
        Route::get('/', [TriageController::class, 'index'])->name('index');
        Route::get('/penilaian/{queueEntryId}', [TriageController::class, 'create'])->name('create');
        Route::post('/penilaian/{queueEntryId}', [TriageController::class, 'store'])->name('store');
    });

    // Role: Dokter / Petugas Poli & Admin
    Route::middleware(['role:doctor,admin'])->prefix('poli')->name('doctor.')->group(function () {
        Route::get('/', [DoctorController::class, 'index'])->name('dashboard');
        Route::post('/panggil/{queueEntryId}', [DoctorController::class, 'call'])->name('call');
        Route::post('/mulai/{queueEntryId}', [DoctorController::class, 'start'])->name('start');
        Route::post('/selesai/{queueEntryId}', [DoctorController::class, 'complete'])->name('complete');
        Route::post('/lewati/{queueEntryId}', [DoctorController::class, 'skip'])->name('skip');

        // Telekonsultasi Dokter
        Route::prefix('telekonsultasi')->name('telekonsultasi.')->group(function () {
            Route::get('/', [DoctorTeleconsultationController::class, 'index'])->name('index');
            Route::get('/{teleconsultation}', [DoctorTeleconsultationController::class, 'show'])->name('show');
            Route::post('/{teleconsultation}/mulai', [DoctorTeleconsultationController::class, 'start'])->name('start');
            Route::get('/{teleconsultation}/ruang-video', [DoctorTeleconsultationController::class, 'room'])->name('room');
            Route::post('/{teleconsultation}/selesai', [DoctorTeleconsultationController::class, 'complete'])->name('complete');
        });
    });

    // Role: Admin
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
        Route::post('/services', [AdminController::class, 'storeService'])->name('services.store');
        Route::put('/services/{serviceId}', [AdminController::class, 'updateService'])->name('services.update');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');

        // Manajemen Jadwal Praktik Dokter
        Route::post('/schedules', [AdminController::class, 'storeSchedule'])->name('schedules.store');
        Route::put('/schedules/{schedule}', [AdminController::class, 'updateSchedule'])->name('schedules.update');
        Route::delete('/schedules/{schedule}', [AdminController::class, 'destroySchedule'])->name('schedules.destroy');
        Route::patch('/schedules/{schedule}/toggle', [AdminController::class, 'toggleSchedule'])->name('schedules.toggle');
    });
});

require __DIR__.'/auth.php';
