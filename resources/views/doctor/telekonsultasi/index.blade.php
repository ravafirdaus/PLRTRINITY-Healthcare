<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-ink tracking-tight">
                        {{ __('Pelayanan Telekonsultasi Poli') }}
                    </h1>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 font-normal mt-0.5">
                    Konsultasi jarak jauh online dengan pasien melalui video konferensi terintegrasi.
                </p>
            </div>

            <!-- Header Filter & Date -->
            <form action="{{ route('doctor.telekonsultasi.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                <!-- Date Filter -->
                <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()"
                       class="py-1.5 px-3 text-xs font-semibold rounded-hospital border-line bg-white text-ink shadow-xs focus:ring-1 focus:ring-primary">

                <!-- Service Selector -->
                <select name="service_id" onchange="this.form.submit()"
                        class="py-1.5 px-3 text-xs font-semibold rounded-hospital border-line bg-white text-ink shadow-xs focus:ring-1 focus:ring-primary">
                    <option value="">Semua Poli Telemedis</option>
                    @foreach($services as $svc)
                        <option value="{{ $svc->id }}" {{ $selectedServiceId == $svc->id ? 'selected' : '' }}>
                            {{ $svc->name }} ({{ $svc->code }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- 4 Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Total Telekonsultasi -->
            <div class="bg-white p-5 rounded-hospital border border-line shadow-xs flex items-center justify-between">
                <div>
                    <div class="text-xs font-medium text-slate-500">Total Sesi Hari Ini</div>
                    <div class="text-2xl font-extrabold text-ink mt-1">{{ $totalCount }}</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">jadwal terdaftar</div>
                </div>
                <div class="w-11 h-11 rounded-hospital bg-primary-50 flex items-center justify-center text-primary shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </div>
            </div>

            <!-- 2. Terjadwal / Menunggu -->
            <div class="bg-white p-5 rounded-hospital border border-line shadow-xs flex items-center justify-between">
                <div>
                    <div class="text-xs font-medium text-slate-500">Menunggu Dimulai</div>
                    <div class="text-2xl font-extrabold text-amber-600 mt-1">{{ $scheduledCount }}</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">pasien dalam jadwal</div>
                </div>
                <div class="w-11 h-11 rounded-hospital bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <!-- 3. Sedang Berlangsung / Siap -->
            <div class="bg-white p-5 rounded-hospital border border-line shadow-xs flex items-center justify-between">
                <div>
                    <div class="text-xs font-medium text-slate-500">Sedang Aktif</div>
                    <div class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $inProgressCount }}</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">dalam ruang video</div>
                </div>
                <div class="w-11 h-11 rounded-hospital bg-emerald-50 flex items-center justify-center text-accent shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.828a5 5 0 010-7.072m7.072 0a5 5 0 010 7.072M12 12h.01"/></svg>
                </div>
            </div>

            <!-- 4. Selesai -->
            <div class="bg-white p-5 rounded-hospital border border-line shadow-xs flex items-center justify-between">
                <div>
                    <div class="text-xs font-medium text-slate-500">Telah Selesai</div>
                    <div class="text-2xl font-extrabold text-primary mt-1">{{ $completedCount }}</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">rekam medis tersimpan</div>
                </div>
                <div class="w-11 h-11 rounded-hospital bg-sky-50 flex items-center justify-center text-primary shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <!-- Teleconsultations Table / List Card -->
        <div class="bg-white rounded-hospital border border-line shadow-xs overflow-hidden">
            <div class="p-5 border-b border-line flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-surface-subtle">
                <div>
                    <h2 class="font-bold text-ink text-sm sm:text-base">Daftar Pasien Telekonsultasi</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Pilih pasien untuk memulai konsultasi video dan mencatat rekam klinis.</p>
                </div>
                <span class="text-xs text-slate-500 font-mono">{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}</span>
            </div>

            @if($teleconsultations->isNotEmpty())
                <div class="divide-y divide-line">
                    @foreach($teleconsultations as $tele)
                        <div class="p-5 hover:bg-surface/50 transition flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <!-- Left: Patient and Schedule Info -->
                            <div class="flex items-start gap-4">
                                <!-- Time Box -->
                                <div class="w-14 h-14 rounded-hospital bg-primary-50 text-primary flex flex-col items-center justify-center shrink-0 border border-primary/20">
                                    <span class="text-base font-extrabold font-mono leading-none">
                                        {{ $tele->scheduled_at ? $tele->scheduled_at->format('H:i') : '-' }}
                                    </span>
                                    <span class="text-[10px] font-semibold mt-1 uppercase text-slate-500">WIB</span>
                                </div>

                                <div class="space-y-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="font-bold text-ink text-sm sm:text-base">{{ $tele->patient->name }}</h3>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $tele->status_badge_class }}">
                                            {{ $tele->status_label }}
                                        </span>
                                    </div>

                                    <div class="text-xs text-slate-600 flex flex-wrap items-center gap-x-3 gap-y-1">
                                        <span class="font-mono text-slate-500">NIK: {{ $tele->patient->masked_nik }}</span>
                                        <span class="text-slate-300">&bull;</span>
                                        <span>JK: <strong>{{ $tele->patient->gender_label }}</strong> ({{ $tele->patient->age }} th)</span>
                                        <span class="text-slate-300">&bull;</span>
                                        <span class="font-semibold text-primary">{{ $tele->service->name }}</span>
                                        @if($tele->patient->phone)
                                            <span class="text-slate-300">&bull;</span>
                                            <span class="text-slate-500 font-mono">{{ $tele->patient->phone }}</span>
                                        @endif
                                    </div>

                                    @if($tele->appointment?->notes)
                                        <p class="text-xs text-slate-600 bg-surface px-2.5 py-1.5 rounded border border-line mt-1 line-clamp-1">
                                            <span class="font-semibold text-slate-700">Keluhan Pasien:</span> {{ $tele->appointment->notes }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Right: Actions -->
                            <div class="flex items-center gap-2.5 self-end md:self-center shrink-0">
                                <a href="{{ route('doctor.telekonsultasi.show', $tele) }}"
                                   class="px-3.5 py-2 rounded-hospital border border-line text-xs font-semibold text-slate-700 hover:bg-surface transition">
                                    Detail Pasien
                                </a>

                                @if($tele->status === 'scheduled' || $tele->status === 'waiting_doctor')
                                    <form method="POST" action="{{ route('doctor.telekonsultasi.start', $tele) }}">
                                        @csrf
                                        <button type="submit"
                                                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-hospital text-xs font-bold transition shadow-xs flex items-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Mulai Konsultasi</span>
                                        </button>
                                    </form>
                                @elseif($tele->status === 'doctor_ready' || $tele->status === 'in_progress')
                                    <a href="{{ route('doctor.telekonsultasi.room', $tele) }}"
                                       class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-hospital text-xs font-bold transition shadow-xs flex items-center gap-1.5 animate-pulse">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        <span>Buka Ruang Video</span>
                                    </a>
                                @elseif($tele->status === 'completed')
                                    <span class="px-3 py-1.5 rounded-hospital bg-slate-100 text-slate-600 text-xs font-semibold">
                                        Konsultasi Selesai
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-12 text-center text-slate-400">
                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    <p class="font-bold text-slate-700 text-sm">Tidak Ada Jadwal Telekonsultasi pada Tanggal Ini</p>
                    <p class="text-xs text-slate-500 mt-0.5">Pilih tanggal atau poli lain untuk melihat jadwal telemedis pasien.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
