<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Pasien — PuskesmasFlow</title>

    <!-- Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F4F7F6] text-ink min-h-screen font-sans antialiased selection:bg-primary selection:text-white flex flex-col justify-center items-center p-4 sm:p-6 lg:p-8">

    <!-- Top Floating Navigation -->
    <div class="w-full max-w-4xl flex items-center justify-between mb-3 px-2">
        <a href="{{ route('home') }}"
           class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-primary transition-colors py-1 px-2.5 rounded-lg hover:bg-white/80">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Beranda</span>
        </a>

        <a href="{{ route('public.queue') }}" target="_blank"
           class="inline-flex items-center gap-1.5 text-xs font-bold text-primary bg-primary/10 hover:bg-primary/15 px-3 py-1.5 rounded-full border border-primary/20 transition-all">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Monitor Antrean</span>
        </a>
    </div>

    <!-- MAIN FLOATING CARD (EcoTrack Layout Style for PuskesmasFlow) -->
    <div class="w-full max-w-4xl bg-white rounded-[26px] sm:rounded-[32px] shadow-[0_20px_60px_-15px_rgba(8,126,139,0.18),0_10px_25px_-5px_rgba(15,23,42,0.08)] border border-slate-100 overflow-hidden flex flex-col md:flex-row">
        
        <!-- LEFT HALF: TEAL BRAND BANNER -->
        <div class="w-full md:w-[46%] bg-[#087E8B] p-8 sm:p-10 lg:p-11 flex flex-col justify-between relative overflow-hidden text-white">
            
            <!-- Subtle Organic Decorative Rings in Background (matching EcoTrack curves) -->
            <div class="pointer-events-none absolute -bottom-24 -left-24 w-80 h-80 rounded-full border-[1.5px] border-white/15"></div>
            <div class="pointer-events-none absolute -bottom-10 -left-10 w-52 h-52 rounded-full border-[1.5px] border-white/15"></div>
            <div class="pointer-events-none absolute top-12 -right-16 w-48 h-48 rounded-full bg-white/5 blur-2xl"></div>

            <!-- Top: White Rounded Card with PuskesmasFlow Logo -->
            <div class="relative z-10">
                <div class="bg-white rounded-2xl px-5 py-3.5 shadow-sm inline-flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="SIPUSKES Medical Center" class="w-10 h-10 object-contain drop-shadow-xs shrink-0">
                    <div>
                        <div class="text-lg font-black tracking-tight text-slate-900 leading-tight">
                            Puskesmas<span class="text-[#087E8B]">Flow</span>
                        </div>
                        <div class="text-[10px] font-semibold text-slate-400 tracking-wide">
                            Healthcare Management System
                        </div>
                    </div>
                </div>
            </div>

            <!-- Middle: Big Bold Heading & Description -->
            <div class="my-8 sm:my-10 relative z-10">
                <h1 class="text-3xl sm:text-4xl lg:text-[40px] font-extrabold leading-[1.15] text-white tracking-tight">
                    Layanan cepat.<br>
                    Kesehatan terjaga.
                </h1>
                <p class="text-white/85 text-xs sm:text-sm leading-relaxed mt-4 max-w-xs">
                    Pantau laporan antrean, jadwal reservasi poli, dan proses pelayanan puskesmas dalam satu sistem yang terintegrasi.
                </p>
            </div>

            <!-- Bottom: Translucent Pill Card -->
            <div class="relative z-10 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 flex items-center gap-3.5 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center shrink-0 text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xs sm:text-sm font-bold text-white leading-snug">
                        Bersama untuk kesehatan
                    </div>
                    <div class="text-[11px] text-white/80 leading-tight mt-0.5">
                        Mulai dari pelayanan puskesmas yang lebih baik.
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT HALF: WHITE LOGIN FORM -->
        <div class="w-full md:w-[54%] p-8 sm:p-12 lg:p-14 bg-white flex flex-col justify-center">
            
            <!-- Category Tag & Heading -->
            <div class="mb-6">
                <span class="text-xs font-bold text-[#087E8B] tracking-wider uppercase block mb-1.5">
                    SELAMAT DATANG &bull; LOGIN PASIEN
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Masuk ke akun Anda
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1.5 leading-relaxed">
                    Login untuk mengakses dashboard PuskesmasFlow.
                </p>
            </div>

            <!-- Session Alerts -->
            @if(session('success'))
                <div class="mb-4 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-3.5 bg-red-50 border border-red-200 text-red-800 rounded-xl text-xs font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('patient.login.store') }}" x-data="{ showPassword: false }">
                @csrf

                <!-- Nomor HP Input -->
                <div class="space-y-1.5 mb-4">
                    <label for="phone" class="block text-xs sm:text-sm font-semibold text-slate-700">
                        Nomor HP / WhatsApp
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required autofocus
                               placeholder="081234567890"
                               class="w-full h-12 pl-10 pr-3.5 text-sm rounded-xl bg-[#EEF4F8] hover:bg-[#E8F0F6] focus:bg-white text-slate-900 border border-transparent focus:border-[#087E8B] focus:ring-2 focus:ring-[#087E8B]/20 transition-all placeholder:text-slate-400 font-medium @error('phone') border-red-500 ring-1 ring-red-500 @enderror">
                    </div>
                    @error('phone')
                        <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="space-y-1.5 mb-6">
                    <label for="password" class="block text-xs sm:text-sm font-semibold text-slate-700">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required
                               placeholder="••••••••"
                               class="w-full h-12 pl-10 pr-11 text-sm rounded-xl bg-[#EEF4F8] hover:bg-[#E8F0F6] focus:bg-white text-slate-900 border border-transparent focus:border-[#087E8B] focus:ring-2 focus:ring-[#087E8B]/20 transition-all placeholder:text-slate-400 font-medium @error('password') border-red-500 ring-1 ring-red-500 @enderror">
                        <button type="button" @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 focus:outline-none transition-colors"
                                title="Tampilkan / Sembunyikan Password">
                            <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button matching EcoTrack -->
                <button type="submit"
                        class="w-full h-12 px-5 bg-[#087E8B] hover:bg-[#076F7B] active:bg-[#055761] text-white text-sm sm:text-base font-bold rounded-xl shadow-md shadow-[#087E8B]/25 hover:shadow-lg hover:shadow-[#087E8B]/30 transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <span>Masuk ke PuskesmasFlow</span>
                    <span aria-hidden="true">&rarr;</span>
                </button>
            </form>

            <!-- Links Below Button -->
            <div class="mt-6 text-center text-xs sm:text-sm text-slate-500 font-medium">
                Belum punya akun?
                <a href="{{ route('patient.register') }}" class="font-bold text-[#087E8B] hover:text-[#055761] hover:underline transition-colors">
                    Daftar sekarang
                </a>
            </div>

            <div class="mt-2.5 text-center text-xs text-slate-400 font-medium">
                Petugas atau Tenaga Medis?
                <a href="{{ route('login') }}" class="font-semibold text-slate-600 hover:text-[#087E8B] hover:underline transition-colors">
                    Login Staf &rarr;
                </a>
            </div>

            <!-- Bottom Brand Subtitle -->
            <div class="mt-8 pt-5 border-t border-slate-100 text-center text-xs text-slate-400 font-medium">
                PuskesmasFlow &bull; Healthcare Management System
            </div>
        </div>
    </div>

</body>
</html>
