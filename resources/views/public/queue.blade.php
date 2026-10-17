<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Monitor Antrean Poliklinik — PuskesmasFlow</title>

    <!-- Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface text-ink min-h-screen flex flex-col justify-between selection:bg-primary selection:text-white font-sans antialiased"
      x-data="queueMonitor()" x-init="initMonitor()">

    <!-- Top Accent Bar -->
    <div class="h-1 bg-primary"></div>

    <!-- MAIN HEADER BAR WITH NAVIGATION -->
    <header class="bg-white border-b border-line px-6 sm:px-10 py-4 shadow-xs">
        <div class="max-w-[1600px] mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            
            <!-- Left: Hospital / Puskesmas Brand -->
            <div class="flex items-center gap-4 w-full md:w-auto justify-between md:justify-start">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="SIPUSKES Medical Center" class="w-11 h-11 object-contain drop-shadow-xs shrink-0">
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-ink leading-tight">
                                Puskesmas<span class="text-primary">Flow</span>
                            </h1>
                            <span class="px-2 py-0.5 rounded-hospital text-[11px] font-bold bg-primary/10 text-primary border border-primary/20 uppercase tracking-wider">
                                Monitor Antrean
                            </span>
                        </div>
                        <p class="text-xs font-medium text-accent">Pusat Informasi & Pemanggilan Pasien Poliklinik</p>
                    </div>
                </div>

                <!-- Mobile Back Button -->
                <a href="{{ route('home') }}"
                   class="md:hidden inline-flex items-center gap-1.5 px-3 py-1.5 rounded-hospital bg-white border border-line text-ink hover:text-primary text-xs font-semibold shadow-xs"
                   title="Kembali ke Beranda">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Beranda</span>
                </a>
            </div>

            <!-- Right: Audio Controls, Date, Live Clock & Back to Home Button -->
            <div class="flex flex-wrap items-center gap-3 sm:gap-4 w-full md:w-auto justify-between md:justify-end">
                <!-- Audio Enable/Mute Button -->
                <div class="flex items-center gap-2">
                    <button type="button" @click="toggleAudio()"
                            class="inline-flex items-center gap-2 h-10 px-3.5 rounded-hospital text-xs font-bold border transition-all active:scale-95 shadow-xs cursor-pointer select-none"
                            :class="audioEnabled ? 'bg-emerald-50 text-emerald-800 border-emerald-300 hover:bg-emerald-100' : 'bg-amber-50 text-amber-900 border-amber-300 hover:bg-amber-100 animate-pulse'">
                        <span class="w-2.5 h-2.5 rounded-full" :class="audioEnabled ? 'bg-emerald-500 animate-ping' : 'bg-amber-500'"></span>
                        <span x-text="audioEnabled ? 'Suara Panggilan Aktif' : 'Aktifkan Suara Panggilan'"></span>
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="audioEnabled">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                        </svg>
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!audioEnabled">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z m12-6l6 6m0-6l-6 6"/>
                        </svg>
                    </button>

                    <button type="button" @click="testAudio()" x-show="audioEnabled"
                            class="inline-flex items-center gap-1.5 h-10 px-3 rounded-hospital bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold border border-line shadow-xs transition-colors cursor-pointer"
                            title="Uji coba nada bel lonceng dan ucapan suara antrean">
                        <span>🔔 Uji Suara</span>
                    </button>
                </div>

                <div class="h-9 w-px bg-line hidden sm:block"></div>

                <!-- Date & Digital Clock -->
                <div class="text-left md:text-right">
                    <div class="text-xs font-semibold text-ink-muted">{{ $todayFormatted }}</div>
                    <div class="text-2xl sm:text-3xl font-extrabold font-mono tracking-tight text-primary mt-0.5" x-text="currentTime">
                        10:28 WIB
                    </div>
                </div>

                <div class="h-9 w-px bg-line hidden sm:block"></div>

                <!-- Desktop Back to Home Button -->
                <a href="{{ route('home') }}"
                   class="hidden md:inline-flex items-center gap-2 h-10 px-4 rounded-hospital bg-white hover:bg-surface-subtle text-ink hover:text-primary text-xs font-semibold border border-line shadow-xs transition-colors group"
                   title="Kembali ke Beranda Utama">
                    <svg class="w-4 h-4 text-primary group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Audio Activation Notice Banner (Muncul bila audio belum diizinkan browser) -->
    <div x-show="!audioEnabled" x-cloak class="max-w-[1600px] mx-auto px-4 sm:px-8 pt-3 w-full">
        <div class="bg-gradient-to-r from-amber-500 to-amber-600 text-white p-3 rounded-hospital shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2.5 text-xs sm:text-sm font-semibold">
                <span class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center shrink-0">🔔</span>
                <span>Aktifkan audio untuk mendengar nada bel lonceng dan panggilan suara otomatis setiap ada pasien yang dipanggil.</span>
            </div>
            <button type="button" @click="enableAudio()"
                    class="px-4 py-1.5 bg-white text-amber-900 font-bold text-xs rounded-hospital shadow-xs hover:bg-amber-50 active:scale-95 transition-all shrink-0 cursor-pointer">
                🔊 Aktifkan Suara Sekarang
            </button>
        </div>
    </div>

    <!-- MAIN DISPLAY AREA (3-COLUMN HOSPITAL MONITOR LAYOUT) -->
    <main class="flex-1 px-4 sm:px-8 py-6 max-w-[1600px] mx-auto w-full flex flex-col justify-center">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
            
            <!-- COLUMN 1: SEDANG DIPANGGIL (lg:col-span-5 — DOMINANT DISPLAY) -->
            <div class="lg:col-span-5 flex flex-col">
                <div class="bg-white rounded-hospital p-7 sm:p-9 shadow-card flex-1 flex flex-col justify-between text-ink border border-line">
                    
                    <!-- Title & Live Badge -->
                    <div class="flex items-center justify-between pb-4 border-b border-line">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-hospital bg-primary/10 text-primary flex items-center justify-center font-bold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-ink tracking-tight">Sedang Dipanggil</h2>
                                <p class="text-[11px] text-ink-muted">Panggilan Aktif Ruang Pemeriksaan</p>
                            </div>
                        </div>

                        <!-- Audio Status Pill -->
                        <button type="button" @click="toggleAudio()"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-hospital border text-xs font-semibold transition cursor-pointer"
                                :class="audioEnabled ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-amber-50 text-amber-800 border-amber-200'">
                            <span class="w-2 h-2 rounded-full" :class="audioEnabled ? 'bg-emerald-500 animate-pulse' : 'bg-amber-500'"></span>
                            <span x-text="audioEnabled ? 'Suara Aktif' : 'Suara Nonaktif'"></span>
                        </button>
                    </div>

                    <!-- Center Body: Hospital Queue Call -->
                    <div class="my-8 text-center">
                        <template x-if="activeCalls.length > 0">
                            <div>
                                <!-- Medical Call Badge -->
                                <div class="w-20 h-20 rounded-hospital bg-primary/10 text-primary border border-primary/20 flex items-center justify-center mx-auto mb-6 shadow-xs">
                                    <svg class="w-10 h-10 animate-pulse text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                                    </svg>
                                </div>

                                <!-- Giant Queue Code -->
                                <div class="text-6xl sm:text-7xl lg:text-8xl font-extrabold font-mono tracking-tight text-primary select-all"
                                     x-text="activeCalls[0].queue_code">
                                    GIG-001
                                </div>

                                <!-- Room / Counter Name -->
                                <div class="text-2xl sm:text-3xl font-bold text-ink mt-4 truncate px-2"
                                     x-text="activeCalls[0].counter_name || activeCalls[0].counter || activeCalls[0].service_name">
                                    Poli Gigi &amp; Mulut
                                </div>

                                <!-- Action Instruction & Replay Audio Button -->
                                <div class="flex flex-wrap items-center justify-center gap-2 mt-4">
                                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-hospital bg-mint border border-primary/20 text-xs sm:text-sm font-semibold text-primary-dark shadow-2xs">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>Silakan Masuk ke Ruang Pemeriksaan</span>
                                    </div>

                                    <button type="button" @click="replayCurrentCall()"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-hospital bg-white hover:bg-slate-50 text-primary border border-primary/30 text-xs font-bold shadow-2xs hover:shadow-xs active:scale-95 transition-all cursor-pointer"
                                            title="Panggil ulang nomor antrean ini dengan nada bel dan suara">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                                        <span>Panggil Ulang</span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <template x-if="activeCalls.length === 0">
                            <div class="py-12">
                                <div class="w-16 h-16 rounded-hospital bg-surface-subtle border border-line text-ink-muted flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                </div>
                                <div class="text-xl font-bold text-ink">Menunggu Giliran Panggilan</div>
                                <div class="text-xs text-ink-muted mt-1.5 max-w-xs mx-auto">
                                    Petugas dokter dan perawat akan memanggil nomor antrean berikutnya sesuai urutan triase klinis.
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Bottom Indicator -->
                    <div class="pt-4 border-t border-line flex items-center justify-between text-xs text-ink-muted font-medium">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-primary"></span>
                            Sistem Informasi Antrean PuskesmasFlow
                        </span>
                        <span class="font-mono text-ink" x-text="'Sinkron: ' + currentTime"></span>
                    </div>
                </div>
            </div>

            <!-- COLUMN 2: ANTREAN BERIKUTNYA (lg:col-span-4) -->
            <div class="lg:col-span-4 flex flex-col">
                <div class="bg-white rounded-hospital p-6 sm:p-7 shadow-card flex-1 flex flex-col text-ink border border-line">
                    <div class="flex items-center justify-between pb-3 border-b border-line mb-4">
                        <div>
                            <h2 class="text-base font-bold text-ink tracking-tight">Antrean Menunggu</h2>
                            <p class="text-[11px] text-ink-muted">Urutan panggilan berikutnya</p>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-hospital bg-surface-subtle border border-line text-ink"
                              x-text="waitingQueues.length + ' Pasien'">
                        </span>
                    </div>

                    <div class="flex-1 space-y-3 overflow-hidden">
                        <template x-if="waitingQueues.length > 0">
                            <div>
                                <template x-for="(q, index) in waitingQueues.slice(0, 5)" :key="q.id || index">
                                    <div class="flex items-center gap-3.5 p-3 rounded-hospital bg-surface-subtle hover:bg-surface transition-colors border border-line mb-2.5">
                                        <!-- Sequence Badge -->
                                        <div class="w-9 h-9 rounded-hospital bg-white border border-line flex items-center justify-center font-bold text-ink text-sm shadow-2xs shrink-0"
                                             x-text="index + 2">
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="font-mono font-bold text-primary text-base leading-tight"
                                                 x-text="q.queue_code">
                                            </div>
                                            <div class="text-xs font-medium text-ink-muted mt-0.5"
                                                 x-text="q.service_name || 'Poliklinik'">
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-hospital border border-emerald-200">
                                                Menunggu
                                            </span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="waitingQueues.length === 0">
                            <div class="h-full flex flex-col items-center justify-center text-center py-12 text-ink-muted">
                                <svg class="w-10 h-10 text-line mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-sm font-semibold text-ink">Tidak ada antrean menunggu</span>
                                <span class="text-xs text-ink-muted mt-0.5">Semua pasien pada poliklinik telah dipanggil.</span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- COLUMN 3: INFORMASI RUANG TUNGGU (lg:col-span-3) -->
            <div class="lg:col-span-3 flex flex-col">
                <div class="bg-white rounded-hospital p-6 sm:p-7 shadow-card flex-1 flex flex-col text-ink border border-line">
                    <div class="pb-3 border-b border-line mb-4">
                        <h2 class="text-base font-bold text-ink tracking-tight">Panduan Pasien</h2>
                        <p class="text-[11px] text-ink-muted">Prosedur layanan ruang tunggu FKTP</p>
                    </div>

                    <div class="flex-1 flex flex-col justify-around space-y-4">
                        <!-- Step 1 -->
                        <div class="flex items-start gap-3">
                            <div class="w-7 h-7 rounded-hospital bg-primary/10 text-primary flex items-center justify-center font-bold text-xs shrink-0 mt-0.5 border border-primary/20">
                                1
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-ink">Ambil Tiket / Check-In</h3>
                                <p class="text-[11px] text-ink-muted mt-0.5 leading-snug">Lakukan check-in reservasi online atau ambil tiket pada anjungan mandiri.</p>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="flex items-start gap-3">
                            <div class="w-7 h-7 rounded-hospital bg-accent/10 text-accent flex items-center justify-center font-bold text-xs shrink-0 mt-0.5 border border-accent/20">
                                2
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-ink">Tunggu Panggilan Suara</h3>
                                <p class="text-[11px] text-ink-muted mt-0.5 leading-snug">Dengarkan nomor antrean Anda yang diumumkan pada layar TV ini.</p>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="flex items-start gap-3">
                            <div class="w-7 h-7 rounded-hospital bg-amber-50 text-amber-700 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5 border border-amber-200">
                                3
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-ink">Siapkan Dokumen Berobat</h3>
                                <p class="text-[11px] text-ink-muted mt-0.5 leading-snug">Siapkan e-KTP/Kartu Keluarga dan kartu BPJS/KIS aktif Anda.</p>
                            </div>
                        </div>

                        <!-- Step 4 -->
                        <div class="flex items-start gap-3">
                            <div class="w-7 h-7 rounded-hospital bg-sky-50 text-sky-700 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5 border border-sky-200">
                                4
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-ink">Masuk Ruang Pemeriksaan</h3>
                                <p class="text-[11px] text-ink-muted mt-0.5 leading-snug">Menuju loket atau ruang dokter sesuai arahan petugas medis.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency / Hotline Callout -->
                    <div class="mt-4 p-3 rounded-hospital bg-surface-subtle border border-line text-[11px] text-ink-muted">
                        <div class="font-bold text-ink flex items-center gap-1.5 mb-1">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            Triase Gawat Darurat
                        </div>
                        Pasien dengan kegawatdaruratan medis segera diarahkan langsung ke Ruang Tindakan / UGD 24 Jam.
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- BOTTOM RUNNING TICKER & FOOTER -->
    <footer class="px-4 sm:px-8 pb-5 pt-1 max-w-[1600px] mx-auto w-full">
        <div class="bg-white rounded-hospital px-5 py-3 border border-line shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <!-- Left: Bell Announcement -->
            <div class="flex items-center gap-2.5 text-ink">
                <div class="w-6 h-6 rounded-hospital bg-primary/10 text-primary flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <span class="font-medium text-ink-muted">
                    <strong class="text-ink">Pemberitahuan:</strong> Mohon tertib menunggu di ruang tunggu poliklinik dan menjaga ketenangan bersama.
                </span>
            </div>

            <!-- Right: Healthcare Slogan -->
            <div class="flex items-center gap-2 text-accent font-semibold text-[11px] shrink-0">
                <svg class="w-4 h-4 text-accent" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
                <span>Mengutamakan Keselamatan &amp; Kenyamanan Pasien</span>
            </div>
        </div>
    </footer>

    <!-- Polling & Live Clock Script -->
    <script>
        function queueMonitor() {
            return {
                currentTime: '10:28 WIB',
                activeCalls: [],
                waitingQueues: [],
                audioEnabled: false,
                isSpeaking: false,
                lastAnnouncedKey: null,
                audioCtx: null,

                initMonitor() {
                    this.updateClock();
                    setInterval(() => this.updateClock(), 1000);

                    // Check if audio was previously enabled
                    if (localStorage.getItem('puskesmasflow_queue_audio') === '1') {
                        this.audioEnabled = true;
                    }

                    // Fetch status immediately and poll every 4 seconds
                    this.fetchData();
                    setInterval(() => this.fetchData(), 4000);

                    // Warm up speech voices if supported
                    if ('speechSynthesis' in window) {
                        window.speechSynthesis.onvoiceschanged = () => {
                            window.speechSynthesis.getVoices();
                        };
                    }
                },

                updateClock() {
                    const now = new Date();
                    this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
                },

                async fetchData() {
                    try {
                        const res = await fetch('{{ route("public.queue.data") }}');
                        if (res.ok) {
                            const data = await res.json();
                            this.activeCalls = data.active_calls || [];
                            this.waitingQueues = data.waiting_queues || [];

                            // Check for newly called queue to announce
                            if (this.activeCalls.length > 0) {
                                const topCall = this.activeCalls[0];
                                const callKey = `${topCall.id}_${topCall.queue_code}_${topCall.called_at || ''}`;

                                if (this.lastAnnouncedKey === null) {
                                    // Initial load, memorize current top call without blast
                                    this.lastAnnouncedKey = callKey;
                                } else if (this.lastAnnouncedKey !== callKey && topCall.status === 'called') {
                                    this.lastAnnouncedKey = callKey;
                                    if (this.audioEnabled) {
                                        this.announceCall(topCall);
                                    }
                                }
                            }
                        }
                    } catch (e) {
                        console.error('Queue poll error:', e);
                    }
                },

                initAudioContext() {
                    try {
                        if (!this.audioCtx) {
                            const AudioContextClass = window.AudioContext || window.webkitAudioContext;
                            if (AudioContextClass) {
                                this.audioCtx = new AudioContextClass();
                            }
                        }
                        if (this.audioCtx && this.audioCtx.state === 'suspended') {
                            this.audioCtx.resume();
                        }
                    } catch (err) {
                        console.warn('AudioContext not available:', err);
                    }
                },

                enableAudio() {
                    this.initAudioContext();
                    this.audioEnabled = true;
                    localStorage.setItem('puskesmasflow_queue_audio', '1');
                    this.playChime().then(() => {
                        this.speakText('Audio pemanggilan antrean aktif.');
                    });
                },

                toggleAudio() {
                    if (!this.audioEnabled) {
                        this.enableAudio();
                    } else {
                        this.audioEnabled = false;
                        localStorage.setItem('puskesmasflow_queue_audio', '0');
                        if ('speechSynthesis' in window) {
                            window.speechSynthesis.cancel();
                        }
                    }
                },

                testAudio() {
                    this.initAudioContext();
                    this.audioEnabled = true;
                    const testCode = this.activeCalls.length > 0 ? this.activeCalls[0].queue_code : 'UMU-001';
                    const testDest = this.activeCalls.length > 0 ? (this.activeCalls[0].counter_name || this.activeCalls[0].counter || this.activeCalls[0].service_name) : 'Poli Umum';

                    this.playChime().then(() => {
                        this.speakCall(testCode, testDest);
                    });
                },

                replayCurrentCall() {
                    if (this.activeCalls.length > 0) {
                        this.initAudioContext();
                        this.audioEnabled = true;
                        this.announceCall(this.activeCalls[0]);
                    } else {
                        this.testAudio();
                    }
                },

                playChime() {
                    return new Promise((resolve) => {
                        try {
                            this.initAudioContext();
                            if (!this.audioCtx) {
                                resolve();
                                return;
                            }

                            const now = this.audioCtx.currentTime;

                            // Melodic 3-tone chime: D5 (587.33Hz) -> F#5 (739.99Hz) -> A5 (880Hz)
                            const notes = [
                                { freq: 587.33, start: now + 0.05, duration: 0.30 },
                                { freq: 739.99, start: now + 0.35, duration: 0.30 },
                                { freq: 880.00, start: now + 0.65, duration: 0.60 }
                            ];

                            notes.forEach((note) => {
                                const osc = this.audioCtx.createOscillator();
                                const gain = this.audioCtx.createGain();

                                osc.type = 'sine';
                                osc.frequency.setValueAtTime(note.freq, note.start);

                                gain.gain.setValueAtTime(0.001, note.start);
                                gain.gain.linearRampToValueAtTime(0.25, note.start + 0.04);
                                gain.gain.exponentialRampToValueAtTime(0.0001, note.start + note.duration);

                                osc.connect(gain);
                                gain.connect(this.audioCtx.destination);

                                osc.start(note.start);
                                osc.stop(note.start + note.duration);
                            });

                            setTimeout(resolve, 1300);
                        } catch (e) {
                            console.error('Chime audio error:', e);
                            resolve();
                        }
                    });
                },

                formatQueueForSpeech(code) {
                    if (!code) return '';
                    const parts = code.split('-');
                    if (parts.length === 2) {
                        const letters = parts[0].split('').join(' '); // e.g. 'U M U'
                        const num = parseInt(parts[1], 10);
                        return `${letters}, nomor ${isNaN(num) ? parts[1] : num}`;
                    }
                    return code.split('').join(' ');
                },

                announceCall(call) {
                    this.playChime().then(() => {
                        const queueCode = call.queue_code;
                        const destination = call.counter_name || call.counter || call.service_name || 'Ruang Pemeriksaan';
                        this.speakCall(queueCode, destination);
                    });
                },

                speakCall(queueCode, destination) {
                    const formattedCode = this.formatQueueForSpeech(queueCode);
                    const sentence = `Nomor antrean, ${formattedCode}, silakan menuju ${destination}.`;
                    this.speakText(sentence);
                },

                speakText(text) {
                    if (!('speechSynthesis' in window)) {
                        console.warn('SpeechSynthesis is not supported in this browser.');
                        return;
                    }

                    try {
                        window.speechSynthesis.cancel();

                        const utterance = new SpeechSynthesisUtterance(text);
                        utterance.lang = 'id-ID';
                        utterance.rate = 0.88;
                        utterance.pitch = 1.0;

                        const voices = window.speechSynthesis.getVoices();
                        const idVoice = voices.find(v => {
                            const l = (v.lang || '').toLowerCase();
                            const n = (v.name || '').toLowerCase();
                            return l.includes('id') || n.includes('indonesia');
                        });

                        if (idVoice) {
                            utterance.voice = idVoice;
                        }

                        utterance.onstart = () => { this.isSpeaking = true; };
                        utterance.onend = () => { this.isSpeaking = false; };
                        utterance.onerror = () => { this.isSpeaking = false; };

                        window.speechSynthesis.speak(utterance);
                    } catch (err) {
                        console.error('Speech synthesis error:', err);
                    }
                }
            };
        }
    </script>
</body>
</html>
