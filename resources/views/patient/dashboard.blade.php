@extends('patient.layouts.app')

@section('title', 'Dashboard Pasien — PuskesmasFlow')

@section('content')
<div class="space-y-6">
    <!-- 3D Welcome Hero Showcase -->
    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-br from-white via-primary-50/40 to-surface-subtle border border-primary/20 shadow-xs p-5 sm:p-7 md:p-8 transition-all hover:shadow-sm">
        <!-- Ambient decorative glow spheres behind -->
        <div class="absolute -top-16 -right-16 w-64 h-64 bg-primary/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-16 -left-16 w-56 h-56 bg-teal-400/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <!-- Left: Greeting & Personal Info -->
            <div class="flex-1 space-y-3.5 text-left w-full">
                <!-- Status Badges -->
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-white text-primary border border-primary/20 shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Portal Resmi Pasien FKTP
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-primary-50 text-primary border border-primary/20 font-mono">
                        <svg class="w-3 h-3 text-primary shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        NIK: {{ $patient->masked_nik }}
                    </span>
                </div>

                <!-- Main Greeting -->
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-tight">
                        Halo, <span class="text-primary">{{ $patient->name }}</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-600 font-normal mt-1.5 leading-relaxed max-w-xl">
                        Selamat datang di portal pelayanan pasien <strong class="text-primary font-semibold">PuskesmasFlow</strong>. Kelola tiket reservasi, akses kartu digital, dan pantau status antrean secara real-time.
                    </p>
                </div>

                <!-- Date & Quick Actions -->
                <div class="pt-1 flex flex-wrap items-center gap-2.5">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/90 backdrop-blur-xs rounded-xl border border-line shadow-2xs text-xs font-semibold text-slate-700">
                        <div class="w-5 h-5 rounded-md bg-primary-50 flex items-center justify-center text-primary shrink-0">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="text-xs">{{ $todayFormatted ?? \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}</span>
                    </div>

                    <a href="{{ route('patient.reservasi.create') }}" class="btn-tap inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-primary hover:bg-primary-hover text-white rounded-xl text-xs font-bold shadow-xs hover:shadow transition-all active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span>Ambil Antrean</span>
                    </a>

                    <a href="{{ route('patient.telekonsultasi.index') }}" class="btn-tap inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-white hover:bg-slate-50 text-slate-700 border border-line rounded-xl text-xs font-bold shadow-2xs hover:shadow-xs transition-all active:scale-95">
                        <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span>Telekonsultasi Video</span>
                    </a>
                </div>
            </div>

            <!-- Right: Official Logo Showcase -->
            <div class="relative shrink-0 flex items-center justify-center">
                <div class="relative group">
                    <!-- Soft radial glow behind logo -->
                    <div class="absolute inset-0 bg-primary/20 rounded-full blur-2xl transform scale-110 pointer-events-none"></div>
                    
                    <img src="{{ asset('images/logo.png') }}" 
                         alt="SIPUSKES Medical Center Logo" 
                         class="relative w-36 h-36 sm:w-44 sm:h-44 md:w-48 md:h-48 lg:w-52 lg:h-52 object-contain animate-float-3d drop-shadow-xl select-none transition-transform duration-500 group-hover:scale-105">
                </div>
            </div>
        </div>
    </div>

    <!-- Active Queue Banner (If patient currently has an active queue today) -->
    @if($activeQueue)
        <div class="bg-white border-2 border-primary/40 rounded-hospital p-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-hospital bg-primary-50 text-primary flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-[11px] font-bold text-primary uppercase tracking-wider">Antrean Aktif Anda Hari Ini</div>
                    <div class="text-2xl font-black font-mono text-ink tracking-tight">{{ $activeQueue->queue_code ?? $activeQueue->queue_number }}</div>
                    <div class="text-xs text-slate-600">{{ $activeQueue->service->name }} &bull; Status: <span class="font-semibold text-primary">{{ $activeQueue->status_label ?? 'Menunggu' }}</span></div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('public.queue') }}" target="_blank"
                   class="btn-tap px-4 py-2 bg-primary hover:bg-primary-hover active:scale-95 text-white rounded-hospital text-xs font-semibold transition shadow-xs">
                    Pantau di Layar Antrean &rarr;
                </a>
            </div>
        </div>
    @endif

    <!-- Kartu Pasien Digital (Otomatis Terbuat dengan Desain Healthcare) -->
    <div class="space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                <h2 class="text-sm sm:text-base font-bold text-ink">Kartu Pasien Digital Resmi (e-KIP)</h2>
            </div>
            <span class="text-[11px] text-slate-500 font-medium">Tunjukkan kartu ini atau nomor peserta saat check-in loket</span>
        </div>

        <div>
            <x-patient-digital-card :patient="$patient" />
        </div>
    </div>

    <!-- 4 Quick Action Cards in Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Buat Reservasi -->
        <a href="{{ route('patient.reservasi.create') }}"
           class="btn-tap bg-white p-5 rounded-hospital border border-line shadow-xs hover:border-primary/40 hover:shadow-sm active:scale-95 transition-all group">
            <div class="w-10 h-10 rounded-hospital bg-primary-50 text-primary flex items-center justify-center mb-3 group-hover:scale-105 group-active:scale-90 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <h3 class="font-bold text-ink text-sm">Buat Reservasi</h3>
            <p class="text-xs text-slate-500 mt-1 leading-relaxed">Daftar antrean online ke poli tujuan faskes.</p>
        </a>

        <!-- 2. Cek Status Antrean -->
        <a href="{{ route('public.queue') }}" target="_blank"
           class="btn-tap bg-white p-5 rounded-hospital border border-line shadow-xs hover:border-primary/40 hover:shadow-sm active:scale-95 transition-all group">
            <div class="w-10 h-10 rounded-hospital bg-sky-50 text-primary flex items-center justify-center mb-3 group-hover:scale-105 group-active:scale-90 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <h3 class="font-bold text-ink text-sm">Cek Status Antrean</h3>
            <p class="text-xs text-slate-500 mt-1 leading-relaxed">Lihat nomor panggilan di layar publik real-time.</p>
        </a>

        <!-- 3. Riwayat Kunjungan -->
        <a href="{{ route('patient.reservasi.history') }}"
           class="btn-tap bg-white p-5 rounded-hospital border border-line shadow-xs hover:border-primary/40 hover:shadow-sm active:scale-95 transition-all group">
            <div class="w-10 h-10 rounded-hospital bg-emerald-50 text-accent flex items-center justify-center mb-3 group-hover:scale-105 group-active:scale-90 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="font-bold text-ink text-sm">Riwayat Kunjungan</h3>
            <p class="text-xs text-slate-500 mt-1 leading-relaxed">Lihat tiket dan riwayat layanan sebelumnya.</p>
        </a>

        <!-- 4. Profil Saya -->
        <a href="{{ route('patient.profile') }}"
           class="btn-tap bg-white p-5 rounded-hospital border border-line shadow-xs hover:border-primary/40 hover:shadow-sm active:scale-95 transition-all group">
            <div class="w-10 h-10 rounded-hospital bg-indigo-50 text-indigo-600 flex items-center justify-center mb-3 group-hover:scale-105 group-active:scale-90 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <h3 class="font-bold text-ink text-sm">Profil Saya</h3>
            <p class="text-xs text-slate-500 mt-1 leading-relaxed">Kelola identitas KTP dan nomor telepon.</p>
        </a>
    </div>

    <!-- Section: Konsultasi Online (Telekonsultasi Video) -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-primary animate-pulse"></span>
                <h2 class="text-base font-bold text-ink">Konsultasi Online (Telekonsultasi)</h2>
            </div>
            <a href="{{ route('patient.telekonsultasi.index') }}" class="text-xs font-semibold text-primary hover:text-primary-hover flex items-center gap-1 transition-colors">
                <span>Lihat Semua Sesi</span>
                <span aria-hidden="true">&rarr;</span>
            </a>
        </div>

        @if($activeTeleconsultations->isNotEmpty())
            <div class="space-y-3">
                @foreach($activeTeleconsultations as $tele)
                    <div class="bg-white p-5 rounded-hospital border-2 {{ in_array($tele->status, ['doctor_ready', 'in_progress']) ? 'border-emerald-500 shadow-sm' : 'border-line shadow-xs' }} flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-start sm:items-center gap-4">
                            <!-- Video Icon Box -->
                            <div class="w-12 h-12 rounded-hospital {{ in_array($tele->status, ['doctor_ready', 'in_progress']) ? 'bg-emerald-50 text-accent border border-emerald-200' : 'bg-primary-50 text-primary border border-primary/20' }} flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </div>

                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="font-bold text-ink text-sm sm:text-base">{{ $tele->service->name }}</h3>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $tele->status_badge_class }}">
                                        {{ $tele->status_label }}
                                    </span>
                                </div>

                                <div class="text-xs text-slate-600 mt-1 flex flex-wrap items-center gap-x-3 gap-y-1">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        <span class="font-medium text-ink">{{ $tele->doctor?->name ?? 'Menunggu Penugasan Dokter' }}</span>
                                    </span>
                                    <span class="text-slate-300">&bull;</span>
                                    <span class="flex items-center gap-1 font-mono text-slate-600">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $tele->scheduled_at ? $tele->scheduled_at->translatedFormat('d M Y, H:i') : '-' }} WIB
                                    </span>
                                </div>

                                @if($tele->status === 'doctor_ready')
                                    <p class="text-[11px] font-bold text-accent mt-1 flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-accent animate-ping inline-block"></span>
                                        Dokter telah memulai ruang konsultasi. Anda dapat bergabung sekarang.
                                    </p>
                                @elseif($tele->status === 'in_progress')
                                    <p class="text-[11px] font-bold text-primary mt-1">
                                        Sesi konsultasi sedang berlangsung aktif.
                                    </p>
                                @elseif(! $tele->isTimeReached())
                                    <p class="text-[11px] text-slate-500 mt-1">
                                        Sesi belum dibuka. Ruang tunggu akan aktif 15 menit sebelum jadwal.
                                    </p>
                                @else
                                    <p class="text-[11px] text-slate-500 mt-1">
                                        Silakan konfirmasi persetujuan di ruang tunggu virtual sementara menunggu dokter.
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2.5 self-end md:self-center shrink-0">
                            <a href="{{ route('patient.telekonsultasi.show', $tele) }}"
                               class="px-3.5 py-2 border border-line text-slate-700 hover:bg-surface rounded-hospital text-xs font-semibold transition-colors">
                                Lihat Detail
                            </a>

                            @if($tele->canPatientEnterRoom())
                                <a href="{{ route('patient.telekonsultasi.room', $tele) }}"
                                   class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-hospital text-xs font-bold transition shadow-xs flex items-center gap-1.5 animate-pulse">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    <span>Masuk Ruang Konsultasi</span>
                                </a>
                            @elseif($tele->isTimeReached())
                                <a href="{{ route('patient.telekonsultasi.show', $tele) }}"
                                   class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-hospital text-xs font-semibold transition shadow-xs flex items-center gap-1.5">
                                    <span>Masuk Ruang Tunggu &rarr;</span>
                                </a>
                            @else
                                <button type="button" disabled
                                        class="px-4 py-2 bg-slate-100 text-slate-400 rounded-hospital text-xs font-semibold cursor-not-allowed">
                                    Belum Dimulai
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-hospital p-5 border border-line shadow-xs flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-hospital bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-ink">Tidak ada telekonsultasi aktif saat ini</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Pilih metode Telekonsultasi Video saat membuat reservasi tiket poli.</p>
                    </div>
                </div>
                <a href="{{ route('patient.reservasi.create') }}" class="px-3 py-1.5 bg-primary-50 hover:bg-primary-100 text-primary border border-primary/20 rounded-hospital text-xs font-semibold transition shrink-0">
                    + Buat Baru
                </a>
            </div>
        @endif
    </div>

    <!-- Section: Reservasi Terbaru -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-bold text-ink">Reservasi Terbaru</h2>
            <a href="{{ route('patient.reservasi.history') }}" class="text-xs font-semibold text-primary hover:text-primary-hover flex items-center gap-1 transition-colors">
                <span>Lihat Semua</span>
                <span aria-hidden="true">&rarr;</span>
            </a>
        </div>

        @if($latestReservation)
            <!-- Single Highlighted Latest Reservation Card -->
            <div class="bg-white p-5 rounded-hospital border border-line shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <!-- Date Box -->
                    <div class="w-14 h-14 rounded-hospital bg-primary-50 text-primary flex flex-col items-center justify-center shrink-0 border border-primary/20">
                        <span class="text-lg font-extrabold leading-none">
                            {{ \Carbon\Carbon::parse($latestReservation->appointment_date)->format('d') }}
                        </span>
                        <span class="text-[10px] font-semibold mt-0.5 uppercase">
                            {{ \Carbon\Carbon::parse($latestReservation->appointment_date)->translatedFormat('M Y') }}
                        </span>
                    </div>

                    <!-- Details -->
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-ink text-sm">
                                Poli {{ $latestReservation->service->name }}
                            </span>
                        </div>
                        <div class="text-xs text-slate-500 font-mono mt-0.5">
                            {{ $latestReservation->reservation_code ?? $latestReservation->appointment_number }}
                        </div>
                        <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 mt-1.5">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>{{ substr($latestReservation->slot_time, 0, 5) }} WIB</span>
                            </span>
                            @if($latestReservation->queueEntry)
                                <span class="font-semibold text-accent">
                                    Antrian: {{ $latestReservation->queueEntry->queue_code }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Status & Action Button -->
                <div class="flex items-center gap-3 self-end md:self-center">
                    @if($latestReservation->status === 'checked_in')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-accent border border-emerald-200">
                            Checked In
                        </span>
                    @elseif($latestReservation->status === 'cancelled')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-600 border border-red-200">
                            Dibatalkan
                        </span>
                    @elseif($latestReservation->status === 'completed')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-sky-50 text-primary border border-sky-200">
                            Selesai
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                            Menunggu
                        </span>
                    @endif

                    <a href="{{ route('patient.reservasi.show', $latestReservation->id) }}"
                       class="px-3.5 py-1.5 border border-line text-slate-700 hover:bg-surface rounded-hospital text-xs font-semibold transition-colors">
                        Lihat Detail
                    </a>
                </div>
            </div>
        @else
            <div class="bg-white rounded-hospital p-8 text-center border border-line shadow-xs text-slate-400">
                <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <p class="font-semibold text-slate-700 text-sm">Belum ada reservasi aktif</p>
                <p class="text-xs text-slate-500 mt-0.5">Buat reservasi antrean online pertama Anda dengan cepat.</p>
                <a href="{{ route('patient.reservasi.create') }}" class="mt-3 inline-block px-4 py-2 bg-primary hover:bg-primary-hover text-white font-semibold text-xs rounded-hospital shadow-xs transition-colors">
                    + Buat Reservasi Sekarang
                </a>
            </div>
        @endif
    </div>

    <!-- Active Upcoming Appointments List (If more than one) -->
    @if($activeAppointments->count() > 1)
        <div class="bg-white rounded-hospital p-5 border border-line shadow-xs space-y-3">
            <h3 class="text-sm font-bold text-ink">Reservasi Mendatang Lainnya</h3>
            <div class="divide-y divide-line">
                @foreach($activeAppointments->skip(1) as $appt)
                    <div class="py-3 flex items-center justify-between gap-4">
                        <div>
                            <div class="font-semibold text-ink text-sm">{{ $appt->service->name }}</div>
                            <div class="text-xs text-slate-500 font-mono mt-0.5">
                                {{ \Carbon\Carbon::parse($appt->appointment_date)->translatedFormat('l, d F Y') }} &bull; {{ substr($appt->slot_time, 0, 5) }} WIB
                            </div>
                        </div>
                        <a href="{{ route('patient.reservasi.show', $appt->id) }}" class="text-xs font-semibold text-primary hover:text-primary-hover">
                            Detail &rarr;
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
