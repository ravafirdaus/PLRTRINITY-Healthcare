<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Staf & Tenaga Medis — PuskesmasFlow</title>

    <!-- Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface-subtle text-ink min-h-screen font-sans antialiased selection:bg-primary selection:text-white">

    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- LEFT SIDEBAR: LIGHT HOSPITAL BRANDING (Visible on desktop lg:flex) -->
        <div class="hidden lg:flex lg:w-1/2 bg-surface-subtle border-r border-line text-ink p-10 lg:p-14 flex-col justify-between relative">
            
            <!-- Top Brand -->
            <div>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    <div class="w-10 h-10 rounded-hospital bg-primary flex items-center justify-center text-white shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xl font-bold tracking-tight text-ink block">Puskesmas<span class="text-primary">Flow</span></span>
                        <span class="text-xs text-ink-muted font-medium block">Sistem Antrean & Triase Terintegrasi</span>
                    </div>
                </a>
            </div>

            <!-- Middle Value Proposition -->
            <div class="my-8 space-y-5">
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded bg-primary/10 text-primary text-[11px] font-semibold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-primary"></span>
                    Portal Tenaga Medis & Staf
                </div>

                <h2 class="text-2xl lg:text-3xl font-semibold text-ink leading-snug">
                    Efisiensi Alur Pelayanan Pasien Puskesmas Secara Terstruktur
                </h2>

                <p class="text-ink-body text-sm leading-relaxed max-w-lg">
                    Sistem terpadu untuk percepatan verifikasi loket, triase kegawatan klinis, pemanggilan poli otomatis, serta monitoring operasional harian.
                </p>

                <!-- 4 Staff Roles Grid (Clean hospital cards with thin borders) -->
                <div class="grid grid-cols-2 gap-3 pt-2">
                    <div class="p-3.5 rounded-hospital bg-white border border-line shadow-xs">
                        <div class="text-xs font-semibold text-ink">Administrator</div>
                        <div class="text-xs text-ink-body mt-0.5">Konfigurasi poli, kapasitas kuota & jadwal layanan.</div>
                    </div>
                    <div class="p-3.5 rounded-hospital bg-white border border-line shadow-xs">
                        <div class="text-xs font-semibold text-ink">Petugas Loket</div>
                        <div class="text-xs text-ink-body mt-0.5">Verifikasi check-in & registrasi pasien walk-in.</div>
                    </div>
                    <div class="p-3.5 rounded-hospital bg-white border border-line shadow-xs">
                        <div class="text-xs font-semibold text-ink">Petugas Triase</div>
                        <div class="text-xs text-ink-body mt-0.5">Penilaian klinis tanda vital & level prioritas.</div>
                    </div>
                    <div class="p-3.5 rounded-hospital bg-white border border-line shadow-xs">
                        <div class="text-xs font-semibold text-ink">Dokter Poli</div>
                        <div class="text-xs text-ink-body mt-0.5">Pemanggilan antrean & pencatatan pelayanan.</div>
                    </div>
                </div>
            </div>

            <!-- Bottom Security Note -->
            <div class="flex items-center justify-between text-xs text-ink-muted pt-6 border-t border-line">
                <span>PuskesmasFlow &bull; Akses Terbatas</span>
                <span>Protokol Keamanan Medis</span>
            </div>
        </div>

        <!-- RIGHT SIDE: LOGIN FORM (Full width on mobile, 1/2 on desktop) -->
        <div class="w-full lg:w-1/2 flex flex-col justify-between p-6 sm:p-10 lg:p-14 bg-white">
            <!-- Top Links: Back to Home -->
            <div class="flex items-center justify-between mb-8">
                <a href="{{ route('home') }}"
                   class="inline-flex items-center gap-2 text-xs font-medium text-ink-body hover:text-primary transition-colors py-1.5 px-3 rounded-hospital hover:bg-surface-subtle border border-transparent hover:border-line">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Kembali ke Beranda</span>
                </a>

                <a href="{{ route('public.queue') }}" target="_blank"
                   class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary bg-primary/10 hover:bg-primary/15 px-3 py-1.5 rounded-hospital border border-primary/20 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full bg-accent"></span>
                    <span>Monitor Antrean</span>
                </a>
            </div>

            <!-- Login Card Container -->
            <div class="max-w-md w-full mx-auto my-auto">
                <!-- Mobile Logo (Hidden on desktop) -->
                <div class="lg:hidden flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-hospital bg-primary flex items-center justify-center text-white shadow-xs shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xl font-bold text-ink tracking-tight block">Puskesmas<span class="text-primary">Flow</span></span>
                        <span class="text-[11px] text-ink-muted font-medium block">Sistem Antrean & Triase</span>
                    </div>
                </div>

                <!-- Form Container -->
                <div class="border border-line rounded-hospital p-6 sm:p-8 bg-white shadow-xs">
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-ink tracking-tight">Login Staf & Tenaga Medis</h1>
                        <p class="text-xs text-ink-body mt-1 leading-relaxed">
                            Khusus Administrator, Petugas Loket, Petugas Triase, dan Dokter Poli. Masukkan email dan password akun dinas Anda.
                        </p>
                    </div>

                    <!-- Session Status Alert -->
                    @if(session('status'))
                        <div class="mb-5 p-3 bg-triage-green-bg border border-triage-green-border text-triage-green-text rounded-hospital text-xs font-medium">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Validation Errors Banner -->
                    @if($errors->any())
                        <div class="mb-5 p-3 bg-triage-red-bg border border-triage-red-border text-triage-red-text rounded-hospital text-xs">
                            <div class="font-bold mb-1">Gagal masuk ke sistem:</div>
                            <ul class="list-disc ms-4 space-y-0.5">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" x-data="{ showPassword: false }">
                        @csrf

                        <!-- Email Input -->
                        <div class="space-y-1.5 mb-4">
                            <label for="email" class="block text-xs font-semibold text-ink uppercase tracking-wider">
                                Email Dinas / Akun Petugas <span class="text-triage-red-DEFAULT">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-ink-muted">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                    </svg>
                                </div>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                       placeholder="nama@puskesmas.go.id"
                                       class="w-full h-10 pl-9 pr-3 text-sm rounded-hospital border border-line bg-white focus:border-primary focus:ring-1 focus:ring-primary text-ink transition-colors placeholder:text-ink-muted">
                            </div>
                        </div>

                        <!-- Password Input with SVG Show/Hide Toggle -->
                        <div class="space-y-1.5 mb-4">
                            <div class="flex items-center justify-between">
                                <label for="password" class="block text-xs font-semibold text-ink uppercase tracking-wider">
                                    Password <span class="text-triage-red-DEFAULT">*</span>
                                </label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-xs font-medium text-primary hover:text-primary-hover">
                                        Lupa Password?
                                    </a>
                                @endif
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-ink-muted">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password"
                                       placeholder="••••••••"
                                       class="w-full h-10 pl-9 pr-10 text-sm rounded-hospital border border-line bg-white focus:border-primary focus:ring-1 focus:ring-primary text-ink transition-colors placeholder:text-ink-muted">
                                <button type="button" @click="showPassword = !showPassword"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-ink-muted hover:text-ink focus:outline-none"
                                        title="Tampilkan / Sembunyikan Password">
                                    <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Me Checkbox -->
                        <div class="flex items-center justify-between mb-5">
                            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                                <input id="remember_me" type="checkbox" name="remember"
                                       class="rounded border-line text-primary shadow-xs focus:ring-primary w-4 h-4">
                                <span class="ms-2 text-xs font-medium text-ink-body">Ingat Saya di Perangkat Ini</span>
                            </label>
                        </div>

                        <!-- Login Button -->
                        <button type="submit"
                                class="w-full h-10 px-4 bg-primary hover:bg-primary-hover text-white text-sm font-medium rounded-hospital shadow-xs flex items-center justify-center gap-2 transition-colors">
                            <span>Masuk ke Sistem Staf</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                    </form>

                    <!-- Switch to Patient Login -->
                    <div class="mt-6 pt-5 border-t border-line">
                        <div class="p-3 bg-surface-subtle rounded-hospital border border-line flex items-center justify-between text-xs">
                            <div class="text-ink font-medium">Anda adalah pasien?</div>
                            <a href="{{ route('patient.login') }}" class="font-semibold text-primary hover:text-primary-hover flex items-center gap-1">
                                <span>Login Pasien</span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Disclaimer -->
            <div class="text-center text-xs text-ink-muted mt-8">
                PuskesmasFlow &bull; Hak Cipta &copy; {{ date('Y') }} Sistem Pelayanan Kesehatan Terpadu
            </div>
        </div>
    </div>

</body>
</html>
