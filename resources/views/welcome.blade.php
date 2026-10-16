<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PuskesmasFlow — Sistem Antrean & Triase Pelayanan Kesehatan</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface-subtle text-ink antialiased selection:bg-primary selection:text-white flex flex-col min-h-screen font-sans">

    <!-- 1. NAVBAR -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs transition-all" x-data="{ mobileOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-6 2xl:px-8">
            <div class="flex items-center justify-between h-16 sm:h-18 gap-2 lg:gap-3 2xl:gap-6">
                
                <!-- Logo & Brand Identity -->
                <a href="{{ route('home') }}" class="flex items-center gap-2 2xl:gap-2.5 shrink-0 group focus:outline-none focus:ring-2 focus:ring-primary/30 rounded-hospital">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 2xl:w-10 2xl:h-10 bg-gradient-to-tr from-primary to-teal-600 rounded-hospital flex items-center justify-center text-white shadow-xs group-hover:scale-105 transition-transform">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-base sm:text-lg 2xl:text-xl font-extrabold tracking-tight text-ink">Puskesmas<span class="text-primary font-black">Flow</span></span>
                        <p class="text-[10px] font-medium text-slate-400 hidden 2xl:block leading-tight">Sistem Kunjungan &amp; Antrean Terpadu</p>
                    </div>
                </a>

                <!-- Desktop Navigation Links (Compact & Polished) -->
                <nav class="hidden lg:flex items-center gap-0.5 xl:gap-1 2xl:gap-1.5 bg-slate-100/70 p-1 rounded-full border border-slate-200/70">
                    <a href="{{ route('home') }}" class="px-2.5 2xl:px-3 py-1.5 rounded-full text-xs xl:text-[13px] 2xl:text-sm font-semibold text-slate-600 hover:text-slate-900 hover:bg-white hover:shadow-2xs transition-all whitespace-nowrap">Beranda</a>
                    <a href="#keunggulan" class="px-2.5 2xl:px-3 py-1.5 rounded-full text-xs xl:text-[13px] 2xl:text-sm font-semibold text-slate-600 hover:text-slate-900 hover:bg-white hover:shadow-2xs transition-all whitespace-nowrap">Keunggulan</a>
                    <a href="#cara-kerja" class="px-2.5 2xl:px-3 py-1.5 rounded-full text-xs xl:text-[13px] 2xl:text-sm font-semibold text-slate-600 hover:text-slate-900 hover:bg-white hover:shadow-2xs transition-all whitespace-nowrap">Cara Kerja</a>
                    <a href="#layanan" class="px-2.5 2xl:px-3 py-1.5 rounded-full text-xs xl:text-[13px] 2xl:text-sm font-semibold text-slate-600 hover:text-slate-900 hover:bg-white hover:shadow-2xs transition-all whitespace-nowrap">Layanan</a>
                    <a href="#biaya-subsidi" class="px-2.5 2xl:px-3 py-1.5 rounded-full text-xs xl:text-[13px] 2xl:text-sm font-semibold text-slate-600 hover:text-slate-900 hover:bg-white hover:shadow-2xs transition-all whitespace-nowrap">Biaya &amp; Subsidi</a>
                    <a href="{{ route('public.queue') }}" class="inline-flex items-center gap-1.5 px-2.5 2xl:px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 hover:bg-emerald-100/90 border border-emerald-200/80 text-xs font-bold whitespace-nowrap transition-all shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Monitor Antrean</span>
                    </a>
                </nav>

                <!-- Action Buttons -->
                <div class="hidden sm:flex items-center gap-1.5 xl:gap-2 2xl:gap-2.5 shrink-0">
                    @auth
                        @if(auth()->user()->isPatient())
                            <a href="{{ route('patient.dashboard') }}" class="h-8.5 2xl:h-10 px-3.5 2xl:px-4 rounded-full bg-primary hover:bg-primary-hover text-white text-xs sm:text-sm font-semibold shadow-xs hover:shadow transition-all flex items-center gap-2 whitespace-nowrap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span>Portal Pasien &rarr;</span>
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="h-8.5 2xl:h-10 px-3.5 2xl:px-4 rounded-full bg-ink hover:bg-slate-800 text-white text-xs sm:text-sm font-semibold shadow-xs hover:shadow transition-all flex items-center gap-2 whitespace-nowrap">
                                <span>Portal Staf &rarr;</span>
                            </a>
                        @endif
                    @else
                        <!-- Pasien Access -->
                        <a href="{{ route('patient.login') }}" class="text-xs 2xl:text-sm font-semibold text-slate-700 hover:text-primary px-2.5 2xl:px-3 py-1.5 rounded-full hover:bg-slate-100 transition whitespace-nowrap flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Masuk Pasien</span>
                        </a>

                        <a href="{{ route('public.appointments.create') }}" class="h-8.5 2xl:h-9.5 px-3.5 2xl:px-4 rounded-full bg-primary hover:bg-primary-hover text-white text-xs sm:text-sm font-bold shadow-xs hover:shadow transition-all flex items-center gap-1.5 whitespace-nowrap">
                            <span>Buat Reservasi</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>

                        <div class="h-4 2xl:h-5 w-px bg-slate-200 hidden lg:block mx-0.5"></div>

                        <!-- Staf Access -->
                        <a href="{{ route('login') }}" class="h-8.5 2xl:h-9 px-2.5 2xl:px-3 rounded-full border border-slate-200/90 bg-white hover:bg-slate-50 text-slate-600 hover:text-slate-900 text-xs font-semibold shadow-2xs transition-colors flex items-center gap-1.5 whitespace-nowrap" title="Login Khusus Staf & Tenaga Medis">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span>Login Staf</span>
                        </a>
                    @endauth
                </div>

                <!-- Mobile Hamburger Button -->
                <div class="flex items-center lg:hidden">
                    <button @click="mobileOpen = !mobileOpen" 
                            type="button" 
                            class="p-2 rounded-hospital text-slate-600 hover:text-ink hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-primary" 
                            aria-label="Toggle Menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                            <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div x-show="mobileOpen" 
             x-cloak 
             @click.away="mobileOpen = false"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="lg:hidden border-b border-line bg-white/95 backdrop-blur-md px-4 pt-3 pb-6 space-y-3 shadow-lg">
            
            <div class="space-y-1">
                <a @click="mobileOpen = false" href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:text-primary hover:bg-slate-50">Beranda</a>
                <a @click="mobileOpen = false" href="#keunggulan" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:text-primary hover:bg-slate-50">Keunggulan</a>
                <a @click="mobileOpen = false" href="#cara-kerja" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:text-primary hover:bg-slate-50">Cara Kerja</a>
                <a @click="mobileOpen = false" href="#layanan" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:text-primary hover:bg-slate-50">Layanan</a>
                <a @click="mobileOpen = false" href="#biaya-subsidi" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:text-primary hover:bg-slate-50">Biaya &amp; Subsidi</a>
                <a href="{{ route('public.queue') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Monitor Antrean Langsung</span>
                </a>
            </div>

            <div class="pt-3 border-t border-line flex flex-col gap-2">
                @auth
                    @if(auth()->user()->isPatient())
                        <a href="{{ route('patient.dashboard') }}" class="w-full text-center py-2.5 bg-primary hover:bg-primary-hover text-white rounded-hospital font-semibold text-sm shadow-xs">
                            Buka Dashboard Pasien
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="w-full text-center py-2.5 bg-ink hover:bg-slate-800 text-white rounded-hospital font-semibold text-sm shadow-xs">
                            Buka Portal Staf
                        </a>
                    @endif
                @else
                    <a href="{{ route('public.appointments.create') }}" class="w-full flex items-center justify-center gap-2 py-2.5 bg-primary hover:bg-primary-hover text-white rounded-hospital font-bold text-sm shadow-xs">
                        <span>Buat Reservasi</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('patient.login') }}" class="text-center py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-hospital font-semibold text-xs transition">
                            Masuk Pasien
                        </a>
                        <a href="{{ route('login') }}" class="text-center py-2 bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 rounded-hospital font-semibold text-xs transition">
                            Login Staf
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-grow">

        <!-- 2. HERO SECTION -->
        <section class="landing-hero relative overflow-hidden py-12 lg:py-20 border-b border-line">
            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-8 items-center">
                    
                    <!-- Left Column: Heading, Description & CTAs -->
                    <div class="lg:col-span-7 space-y-4 sm:space-y-5 text-center lg:text-left">
                        
                        <!-- Eyebrow Pill -->
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-mint text-primary-dark text-xs font-semibold tracking-wide border border-primary/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                            <span>Puskesmas Digital</span>
                        </div>

                        <!-- Main Heading -->
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-ink leading-[1.15] max-w-xl mx-auto lg:mx-0">
                            Pelayanan Puskesmas Lebih Teratur, Dimulai dari Sini.
                        </h1>

                        <!-- Description -->
                        <p class="text-base sm:text-lg text-ink-body leading-relaxed max-w-xl mx-auto lg:mx-0">
                            Daftar kunjungan secara online, pantau status antrean, dan dapatkan estimasi waktu pelayanan dengan lebih mudah.
                        </p>

                        <!-- CTA Group -->
                        <div class="pt-1 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3">
                            <a href="{{ auth()->check() && auth()->user()->isPatient() ? route('patient.reservasi.create') : route('public.appointments.create') }}" 
                               class="w-full sm:w-auto h-11 px-6 bg-primary hover:bg-primary-hover text-white font-medium text-sm rounded-hospital shadow-xs transition-colors text-center flex items-center justify-center gap-2">
                                <span>Buat Reservasi</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>

                            <a href="{{ route('public.queue') }}" 
                               class="w-full sm:w-auto h-11 px-6 bg-white hover:bg-surface-subtle text-ink font-medium text-sm rounded-hospital border border-line shadow-xs transition-colors text-center flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>Pantau Antrean</span>
                            </a>
                        </div>

                        <!-- Single Concise Row of Key Benefits -->
                        <div class="pt-2 text-xs text-ink-muted flex flex-wrap items-center justify-center lg:justify-start gap-x-4 gap-y-1">
                            <span>Pendaftaran Terjadwal</span>
                            <span class="text-line">•</span>
                            <span>Nomor Antrean Transparan</span>
                            <span class="text-line">•</span>
                            <span>Prioritas Triase Terarah</span>
                        </div>
                    </div>

                    <!-- Right Column: Simplified Queue Preview Panel -->
                    <div class="lg:col-span-5">
                        <div class="queue-preview-card bg-white/95 backdrop-blur rounded-panel border border-white shadow-panel p-5 sm:p-6 space-y-4">
                            
                            <!-- Header of Preview -->
                            <div class="flex items-center justify-between pb-3 border-b border-line">
                                <div>
                                    <h2 class="text-sm font-bold text-ink">Preview Antrean</h2>
                                    <p class="text-[11px] text-ink-muted">Papan panggilan ruang pemeriksaan</p>
                                </div>
                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-surface-subtle text-ink-muted border border-line">
                                    Simulasi Antrean
                                </span>
                            </div>

                            <!-- Calling Queue Section -->
                            <div class="bg-mint border border-primary/20 rounded-hospital p-4 text-ink">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-primary text-white">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                        Sedang Dipanggil
                                    </span>
                                    <span class="text-[11px] font-medium text-ink-muted">Ruang Konsultasi</span>
                                </div>
                                
                                <div class="flex items-baseline justify-between mt-1">
                                    <div>
                                        <div class="text-3xl font-extrabold font-mono tracking-wider text-primary">
                                            {{ $callingQueue ? $callingQueue->queue_code : 'UMU-001' }}
                                        </div>
                                        <p class="text-xs font-semibold text-ink mt-0.5">
                                            {{ $callingQueue && $callingQueue->service ? $callingQueue->service->name : 'Poli Umum' }}
                                        </p>
                                    </div>
                                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-white text-emerald-700 border border-emerald-200">
                                        Sedang Dilayani
                                    </span>
                                </div>
                            </div>

                            <!-- Next Queue Section (Max 2 rows) -->
                            <div class="space-y-2 pt-1">
                                <div class="text-[11px] font-semibold text-ink-muted uppercase tracking-wider">
                                    Antrean Berikutnya
                                </div>

                                @if($nextQueues->count() > 0)
                                    @foreach($nextQueues->take(2) as $nq)
                                        <div class="flex items-center justify-between p-2.5 rounded-hospital bg-surface-subtle border border-line text-xs">
                                            <div class="flex items-center gap-2.5">
                                                <span class="font-mono font-bold text-ink">{{ $nq->queue_code }}</span>
                                                <span class="text-ink-body">{{ $nq->service->name }}</span>
                                            </div>
                                            <span class="text-[10px] font-medium text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                                Menunggu
                                            </span>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="flex items-center justify-between p-2.5 rounded-hospital bg-surface-subtle border border-line text-xs">
                                        <div class="flex items-center gap-2.5">
                                            <span class="font-mono font-bold text-ink">UMU-002</span>
                                            <span class="text-ink-body">Poli Umum</span>
                                        </div>
                                        <span class="text-[10px] font-medium text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                            Menunggu
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between p-2.5 rounded-hospital bg-surface-subtle border border-line text-xs">
                                        <div class="flex items-center gap-2.5">
                                            <span class="font-mono font-bold text-ink">GIG-001</span>
                                            <span class="text-ink-body">Poli Gigi &amp; Mulut</span>
                                        </div>
                                        <span class="text-[10px] font-medium text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                            Menunggu
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Status Footer Note -->
                            <div class="pt-2 text-center border-t border-line">
                                <p class="text-[11px] text-ink-muted">
                                    Urutan panggilan menyesuaikan prioritas klinis pasien.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- 3. FITUR UTAMA (TIGA PILAR STANDAR PELAYANAN PUSKESMAS) -->
        <section id="keunggulan" class="py-14 bg-surface-subtle border-b border-line">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-2xl mx-auto mb-10">
                    <h2 class="text-2xl font-bold text-ink tracking-tight">
                        Tiga Pilar Standar Pelayanan Puskesmas
                    </h2>
                    <p class="text-xs sm:text-sm text-ink-body mt-2 leading-relaxed">
                        Fasilitas kesehatan yang tertata rapi, transparan, dan mengutamakan kenyamanan setiap pasien.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <!-- Fitur 1: Reservasi Terjadwal -->
                    <div class="bg-white rounded-hospital p-6 border border-line shadow-xs flex flex-col">
                        <div class="w-10 h-10 rounded-hospital bg-mint text-primary-dark flex items-center justify-center font-bold mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-ink">Reservasi Terjadwal</h3>
                        <p class="text-xs text-ink-body mt-2 leading-relaxed">
                            Pilih poliklinik dan slot jam kedatangan sebelum menuju puskesmas untuk mengurangi waktu tunggu di lokasi.
                        </p>
                    </div>

                    <!-- Fitur 2: Informasi Antrean -->
                    <div class="bg-white rounded-hospital p-6 border border-line shadow-xs flex flex-col">
                        <div class="w-10 h-10 rounded-hospital bg-mint text-primary-dark flex items-center justify-center font-bold mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-ink">Informasi Antrean</h3>
                        <p class="text-xs text-ink-body mt-2 leading-relaxed">
                            Pantau nomor panggilan antrean dan estimasi giliran secara terbuka melalui layar ruang tunggu maupun ponsel.
                        </p>
                    </div>

                    <!-- Fitur 3: Prioritas Triase -->
                    <div class="bg-white rounded-hospital p-6 border border-line shadow-xs flex flex-col">
                        <div class="w-10 h-10 rounded-hospital bg-mint text-primary-dark flex items-center justify-center font-bold mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-ink">Prioritas Triase</h3>
                        <p class="text-xs text-ink-body mt-2 leading-relaxed">
                            Petugas kesehatan menilai kondisi klinis pasien untuk penanganan tepat sesuai tingkat kegawatan.
                        </p>
                    </div>

                </div>
            </div>
        </section>

        <!-- 4. ALUR PENGGUNAAN (CARA KERJA 3 LANGKAH) -->
        <section id="cara-kerja" class="py-14 bg-white border-b border-line">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-2xl mx-auto mb-10">
                    <h2 class="text-2xl font-bold text-ink tracking-tight">
                        Alur Pelayanan Pasien
                    </h2>
                    <p class="text-xs sm:text-sm text-ink-body mt-2 leading-relaxed">
                        Tiga langkah mudah untuk menyelesaikan kunjungan berobat Anda.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <!-- Langkah 1 -->
                    <div class="bg-surface-subtle rounded-hospital p-6 border border-line space-y-3">
                        <div class="w-8 h-8 rounded-hospital bg-primary text-white flex items-center justify-center font-bold text-xs shadow-xs">
                            1
                        </div>
                        <h3 class="text-sm font-bold text-ink">Pilih Jadwal Poliklinik</h3>
                        <p class="text-xs text-ink-body leading-relaxed">
                            Pilih poli tujuan, tanggal kunjungan, dan slot waktu yang tersedia melalui website secara mandiri.
                        </p>
                    </div>

                    <!-- Langkah 2 -->
                    <div class="bg-surface-subtle rounded-hospital p-6 border border-line space-y-3">
                        <div class="w-8 h-8 rounded-hospital bg-primary text-white flex items-center justify-center font-bold text-xs shadow-xs">
                            2
                        </div>
                        <h3 class="text-sm font-bold text-ink">Check-in &amp; Skrining Triase</h3>
                        <p class="text-xs text-ink-body leading-relaxed">
                            Saat tiba di faskes, lakukan konfirmasi di loket. Tenaga perawat melakukan pemeriksaan tanda vital dan skrining triase.
                        </p>
                    </div>

                    <!-- Langkah 3 -->
                    <div class="bg-surface-subtle rounded-hospital p-6 border border-line space-y-3">
                        <div class="w-8 h-8 rounded-hospital bg-primary text-white flex items-center justify-center font-bold text-xs shadow-xs">
                            3
                        </div>
                        <h3 class="text-sm font-bold text-ink">Pemanggilan Masuk Poli</h3>
                        <p class="text-xs text-ink-body leading-relaxed">
                            Pasien menunggu panggilan di ruang tunggu poliklinik dan masuk saat nomor antrean dipanggil oleh dokter.
                        </p>
                    </div>

                </div>
            </div>
        </section>

        <!-- 5. INFORMASI LAYANAN DAN ANTREAN -->
        <section id="layanan" class="py-14 bg-surface-subtle border-b border-line">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                
                <!-- Layanan Poli -->
                <div>
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-4">
                        <div>
                            <h2 class="text-2xl font-bold text-ink tracking-tight">
                                Poliklinik &amp; Layanan Tersedia
                            </h2>
                            <p class="text-xs sm:text-sm text-ink-body mt-1">
                                Pilih layanan poliklinik sesuai kebutuhan medis Anda.
                            </p>
                        </div>

                        <a href="{{ auth()->check() && auth()->user()->isPatient() ? route('patient.reservasi.create') : route('public.appointments.create') }}" 
                           class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary hover:text-primary-hover whitespace-nowrap">
                            <span>Buat Reservasi Baru</span>
                            <span>&rarr;</span>
                        </a>
                    </div>

                    @if(isset($services) && $services->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            @foreach($services as $service)
                                <div class="bg-white rounded-hospital p-5 border border-line shadow-xs flex flex-col justify-between">
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="px-2 py-0.5 rounded text-xs font-bold font-mono bg-surface-subtle text-ink border border-line">
                                                {{ $service->code }}
                                            </span>
                                            <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                                Buka
                                            </span>
                                        </div>
                                        <h3 class="text-sm font-bold text-ink">
                                            {{ $service->name }}
                                        </h3>
                                        <p class="text-xs text-ink-body leading-relaxed line-clamp-2">
                                            {{ $service->description ?? 'Pemeriksaan umum dan konsultasi dokter.' }}
                                        </p>
                                    </div>

                                    <div class="mt-4 pt-3 border-t border-line flex items-center justify-between text-xs text-ink-muted">
                                        <span>Durasi: ±{{ $service->slot_duration_minutes ?? 20 }} mnt</span>
                                        <a href="{{ auth()->check() && auth()->user()->isPatient() ? route('patient.reservasi.create') : route('public.appointments.create') }}?service_id={{ $service->id }}" 
                                           class="text-primary font-semibold hover:underline">
                                            Pilih &rarr;
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-white rounded-hospital p-8 text-center border border-line text-xs text-ink-muted">
                            Belum ada poliklinik aktif yang terdaftar.
                        </div>
                    @endif
                </div>

                <!-- Snapshot Pemantauan Antrean -->
                <div class="bg-white rounded-hospital p-6 border border-line shadow-xs space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-line">
                        <div>
                            <h3 class="text-sm font-bold text-ink">Snapshot Antrean Poliklinik Hari Ini</h3>
                            <p class="text-[11px] text-ink-muted">Pemantauan nomor giliran yang sedang berjalan di ruang tunggu</p>
                        </div>
                        <a href="{{ route('public.queue') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary hover:text-primary-hover">
                            <span>Buka Monitor Publik Penuh</span>
                            <span>&rarr;</span>
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="bg-surface-subtle border-b border-line text-ink font-semibold uppercase text-[10px] tracking-wider">
                                    <th class="py-2.5 px-3">Kode Antrean</th>
                                    <th class="py-2.5 px-3">Poli Tujuan</th>
                                    <th class="py-2.5 px-3">Status Pelayanan</th>
                                    <th class="py-2.5 px-3 text-right">Perkiraan Waktu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-line text-ink-body">
                                <tr>
                                    <td class="py-2.5 px-3 font-mono font-bold text-sm text-primary">{{ $callingQueue ? $callingQueue->queue_code : 'UMU-001' }}</td>
                                    <td class="py-2.5 px-3 font-medium text-ink">{{ $callingQueue && $callingQueue->service ? $callingQueue->service->name : 'Poli Umum' }}</td>
                                    <td class="py-2.5 px-3"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">Sedang Dilayani</span></td>
                                    <td class="py-2.5 px-3 text-right font-mono text-ink">Sedang Dipanggil</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3 font-mono font-bold text-sm text-ink">UMU-002</td>
                                    <td class="py-2.5 px-3 font-medium text-ink">Poli Umum</td>
                                    <td class="py-2.5 px-3"><span class="px-2 py-0.5 rounded text-[10px] font-medium bg-surface-subtle text-ink-muted border border-line">Menunggu</span></td>
                                    <td class="py-2.5 px-3 text-right font-mono text-ink-muted">± 15 mnt</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3 font-mono font-bold text-sm text-ink">GIG-001</td>
                                    <td class="py-2.5 px-3 font-medium text-ink">Poli Gigi &amp; Mulut</td>
                                    <td class="py-2.5 px-3"><span class="px-2 py-0.5 rounded text-[10px] font-medium bg-surface-subtle text-ink-muted border border-line">Menunggu</span></td>
                                    <td class="py-2.5 px-3 text-right font-mono text-ink-muted">± 25 mnt</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </section>

        <!-- 5.5. TRANSPARANSI SUBSIDI & PEMBAYARAN BERDASARKAN GOLONGAN -->
        <section id="biaya-subsidi" class="py-14 bg-white border-b border-line">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-3xl mx-auto mb-10">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-200 mb-3">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Kepastian Biaya &amp; Akuntabilitas Faskes
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight">
                        Transparansi Subsidi &amp; Pembayaran Berdasarkan Golongan
                    </h2>
                    <p class="text-xs sm:text-sm text-ink-body mt-2 leading-relaxed">
                        Puskesmas menjamin kepastian status biaya bagi seluruh pasien. Terbuka, tanpa iuran terselubung, dan bebas dari pungutan liar.
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">
                    
                    <!-- PILAR 1: Subsidi Penuh (Menengah ke Bawah) -->
                    <div class="rounded-hospital p-6 sm:p-8 bg-gradient-to-b from-emerald-50/70 via-white to-white border-2 border-emerald-400 shadow-xs flex flex-col justify-between relative overflow-hidden">
                        <div class="absolute -right-12 -top-12 w-36 h-36 bg-emerald-100/50 rounded-full blur-xl pointer-events-none"></div>

                        <div>
                            <div class="flex items-center justify-between gap-3 mb-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-100 text-emerald-900 border border-emerald-300">
                                    Subsidi Penuh (Pemerintah)
                                </span>
                                <span class="text-[11px] font-semibold text-emerald-700 bg-white px-2.5 py-0.5 rounded-full border border-emerald-200">
                                    JKN-KIS / PBI / Non-Mampu
                                </span>
                            </div>

                            <h3 class="text-lg sm:text-xl font-bold text-ink">
                                Golongan Menengah ke Bawah
                            </h3>
                            <p class="text-xs text-ink-body mt-1 leading-relaxed">
                                Diperuntukkan bagi masyarakat penerima bantuan iuran jaminan kesehatan dan keluarga prasejahtera.
                            </p>

                            <!-- Status Box Utama: Bebas Biaya (Rp 0) -->
                            <div class="mt-5 p-4 rounded-hospital bg-emerald-600 text-white shadow-xs">
                                <div class="text-[11px] font-semibold uppercase tracking-wider text-emerald-100">Status Pembayaran Pasien</div>
                                <div class="text-3xl sm:text-4xl font-extrabold font-mono tracking-tight my-1">
                                    Bebas Biaya (Rp 0)
                                </div>
                                <p class="text-xs text-emerald-100 leading-snug">
                                    Ditampilkan status <strong>"Bebas Biaya (Rp 0)"</strong> secara transparan tanpa iuran bulanan dan tanpa tagihan sama sekali.
                                </p>
                            </div>

                            <!-- Rincian Layanan Rp 0 -->
                            <div class="mt-5 space-y-2.5">
                                <p class="text-xs font-bold text-ink uppercase tracking-wider">Cakupan Pelayanan Bebas Biaya:</p>
                                
                                <div class="flex items-start gap-2.5 text-xs text-ink-body">
                                    <div class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <div class="flex-1 flex justify-between">
                                        <span>Pendaftaran loket &amp; penerbitan antrean</span>
                                        <strong class="text-emerald-700 font-mono">Rp 0</strong>
                                    </div>
                                </div>

                                <div class="flex items-start gap-2.5 text-xs text-ink-body">
                                    <div class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <div class="flex-1 flex justify-between">
                                        <span>Skrining tanda vital &amp; triase klinis</span>
                                        <strong class="text-emerald-700 font-mono">Rp 0</strong>
                                    </div>
                                </div>

                                <div class="flex items-start gap-2.5 text-xs text-ink-body">
                                    <div class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <div class="flex-1 flex justify-between">
                                        <span>Pemeriksaan dokter umum / gigi / KIA</span>
                                        <strong class="text-emerald-700 font-mono">Rp 0</strong>
                                    </div>
                                </div>

                                <div class="flex items-start gap-2.5 text-xs text-ink-body">
                                    <div class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <div class="flex-1 flex justify-between">
                                        <span>Paket obat generik formularium faskes</span>
                                        <strong class="text-emerald-700 font-mono">Rp 0</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Catatan Subsidi Penuh -->
                        <div class="mt-6 pt-4 border-t border-emerald-200/80 text-[11px] text-emerald-900 bg-emerald-50/50 -mx-6 -mb-6 p-4 rounded-b-hospital">
                            <div class="flex items-center gap-2 font-bold">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Jaminan Kepastian Pasien:
                            </div>
                            <p class="text-[11px] text-slate-600 mt-0.5">
                                Cukup tunjukkan NIK / e-KTP atau Kartu BPJS/KIS aktif saat konfirmasi di loket. Pasien pulang tanpa membawa surat tagihan kasir.
                            </p>
                        </div>
                    </div>

                    <!-- PILAR 2: Mandiri/Reguler (Menengah ke Atas) -->
                    <div class="rounded-hospital p-6 sm:p-8 bg-gradient-to-b from-sky-50/60 via-white to-white border border-sky-300 shadow-xs flex flex-col justify-between relative overflow-hidden">
                        <div class="absolute -right-12 -top-12 w-36 h-36 bg-sky-100/50 rounded-full blur-xl pointer-events-none"></div>

                        <div>
                            <div class="flex items-center justify-between gap-3 mb-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-sky-100 text-sky-900 border border-sky-200">
                                    Mandiri / Reguler
                                </span>
                                <span class="text-[11px] font-semibold text-slate-600 bg-white px-2.5 py-0.5 rounded-full border border-line">
                                    Pasien Umum / Non-Subsidi
                                </span>
                            </div>

                            <h3 class="text-lg sm:text-xl font-bold text-ink">
                                Golongan Menengah ke Atas
                            </h3>
                            <p class="text-xs text-ink-body mt-1 leading-relaxed">
                                Pelayanan mandiri transparan dengan estimasi rincian biaya yang pasti sebelum tindakan dilakukan.
                            </p>

                            <!-- Estimasi Rincian Biaya Transparan -->
                            <div class="mt-5 p-4 rounded-hospital bg-surface-subtle border border-sky-200 space-y-2">
                                <div class="flex items-center justify-between pb-2 border-b border-line">
                                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Estimasi Rincian Biaya Dasar</span>
                                    <span class="text-[10px] font-semibold text-sky-700 bg-sky-50 px-2 py-0.5 rounded border border-sky-200">Perda Tarif Resmi</span>
                                </div>
                                <div class="flex justify-between text-xs text-ink-body">
                                    <span>Retribusi Pendaftaran &amp; Administrasi</span>
                                    <span class="font-mono font-bold text-ink">Rp 10.000</span>
                                </div>
                                <div class="flex justify-between text-xs text-ink-body">
                                    <span>Jasa Konsultasi Dokter &amp; Skrining Klinis</span>
                                    <span class="font-mono font-bold text-ink">Rp 15.000</span>
                                </div>
                                <div class="flex justify-between items-baseline pt-2 border-t border-line text-ink">
                                    <span class="text-xs font-bold">Total Estimasi Kunjungan Dasar:</span>
                                    <span class="text-lg font-extrabold font-mono text-primary">Rp 25.000</span>
                                </div>
                                <p class="text-[10px] text-slate-500 pt-1">
                                    *Tindakan tambahan atau obat khusus dihitung transparan sesuai regulasi tarif tanpa mark-up liar.
                                </p>
                            </div>

                            <!-- Opsi Metode Pembayaran -->
                            <div class="mt-5 space-y-3">
                                <p class="text-xs font-bold text-ink uppercase tracking-wider">Opsi Metode Pembayaran Fleksibel:</p>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div class="p-3 rounded-hospital bg-white border border-line flex items-start gap-2.5 shadow-2xs">
                                        <div class="w-7 h-7 rounded-md bg-sky-100 text-sky-700 flex items-center justify-center shrink-0 mt-0.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold text-ink">On-the-spot di Loket</h4>
                                            <p class="text-[11px] text-slate-500 mt-0.5">Bayar tunai atau kartu debit langsung di kasir loket faskes.</p>
                                        </div>
                                    </div>

                                    <div class="p-3 rounded-hospital bg-white border border-line flex items-start gap-2.5 shadow-2xs">
                                        <div class="w-7 h-7 rounded-md bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold text-ink">Pembayaran Digital</h4>
                                            <p class="text-[11px] text-slate-500 mt-0.5">Scan QRIS (semua e-wallet / mobile banking) atau transfer VA.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Catatan Mandiri -->
                        <div class="mt-6 pt-4 border-t border-sky-200/80 text-[11px] text-sky-900 bg-sky-50/50 -mx-6 -mb-6 p-4 rounded-b-hospital">
                            <div class="flex items-center gap-2 font-bold">
                                <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                                Bukti Pembayaran Resmi:
                            </div>
                            <p class="text-[11px] text-slate-600 mt-0.5">
                                Setiap transaksi diterbitkan tanda terima / struk resmi berstempel faskes sebagai bukti pembayaran yang sah.
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- 6. INFORMASI KESELAMATAN PASIEN -->
        <section class="py-12 bg-white border-b border-line">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-amber-50 border border-amber-200 rounded-hospital p-5 sm:p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-hospital bg-amber-500 text-white flex items-center justify-center font-bold shrink-0 mt-0.5 shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-amber-900">
                                Maklumat Keselamatan Pasien &amp; Kondisi Darurat
                            </h2>
                            <p class="text-xs text-amber-800/90 mt-1 leading-relaxed max-w-3xl">
                                Kondisi darurat yang membutuhkan pertolongan medis segera tidak boleh ditunda demi menunggu jadwal reservasi online. Pasien dengan kegawatdaruratan dapat langsung mendatangi Ruang Tindakan / IGD Puskesmas untuk penanganan medis cepat oleh petugas.
                            </p>
                        </div>
                    </div>
                    <div class="shrink-0 w-full md:w-auto text-left md:text-right">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-hospital bg-white text-amber-900 text-xs font-semibold border border-amber-200 shadow-xs">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            Jalur Tindakan Segera
                        </span>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- 7. FOOTER -->
    <footer class="bg-surface-subtle pt-10 pb-8 text-ink-muted text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-8 border-b border-line">
                
                <!-- Col 1: Brand Info -->
                <div class="space-y-3 md:col-span-1">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-hospital bg-primary/10 border border-primary/20 flex items-center justify-center text-primary font-bold">
                            <svg class="w-4 h-4 text-primary" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M10 4a1 1 0 011-1h2a1 1 0 011 1v5h5a1 1 0 011 1v2a1 1 0 01-1 1h-5v5a1 1 0 01-1 1h-2a1 1 0 01-1-1v-5H5a1 1 0 01-1-1v-2a1 1 0 011-1h5V4z"/>
                            </svg>
                        </div>
                        <span class="text-base font-bold text-ink tracking-tight">Puskesmas<span class="text-primary">Flow</span></span>
                    </div>
                    <p class="leading-relaxed text-ink-muted text-xs">
                        Sistem manajemen antrean dan reservasi poliklinik untuk kenyamanan dan keteraturan pelayanan kesehatan puskesmas.
                    </p>
                </div>

                <!-- Col 2: Layanan Pasien -->
                <div class="space-y-2">
                    <p class="font-bold text-ink text-xs uppercase tracking-wider mb-2.5">Layanan Pasien</p>
                    <p><a href="{{ route('public.appointments.create') }}" class="hover:text-primary transition-colors">Buat Reservasi Online</a></p>
                    <p><a href="{{ route('patient.register') }}" class="hover:text-primary transition-colors">Daftar Akun Pasien</a></p>
                    <p><a href="{{ route('patient.login') }}" class="hover:text-primary transition-colors">Masuk Portal Pasien</a></p>
                    <p><a href="{{ route('public.appointments.search') }}" class="hover:text-primary transition-colors">Cek Tiket Reservasi</a></p>
                </div>

                <!-- Col 3: Informasi Operasional -->
                <div class="space-y-2">
                    <p class="font-bold text-ink text-xs uppercase tracking-wider mb-2.5">Operasional</p>
                    <p><a href="{{ route('public.queue') }}" class="hover:text-primary transition-colors">Monitor Antrean Publik</a></p>
                    <p><a href="{{ route('login') }}" class="hover:text-primary transition-colors">Portal Login Staf</a></p>
                    <p><a href="{{ route('demo') }}" class="hover:text-primary text-slate-500 transition-colors">Akses Akun Demo Evaluator</a></p>
                </div>

                <!-- Col 4: Jam Layanan -->
                <div class="space-y-2 text-xs">
                    <p class="font-bold text-ink text-xs uppercase tracking-wider mb-2.5">Jam Layanan</p>
                    <p class="text-ink">Senin – Sabtu: 08:00 – 15:00 WIB</p>
                    <p class="text-ink-muted">UGD / Tindakan Darurat: 24 Jam</p>
                    <p class="text-ink-muted mt-2">Puskesmas Kecamatan</p>
                </div>

            </div>

            <!-- Bottom Copyright -->
            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-ink-muted">
                <p>&copy; {{ date('Y') }} PuskesmasFlow. Seluruh hak cipta dilindungi.</p>
                <div class="flex items-center gap-3">
                    <a href="{{ route('demo') }}" class="text-slate-400 hover:text-primary transition-colors">Akun Demo</a>
                    <span>•</span>
                    <span>Sistem Reservasi &amp; Antrean Faskes</span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
