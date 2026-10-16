@extends('patient.layouts.app')

@section('title', 'Daftar Telekonsultasi Video — PuskesmasFlow')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-ink tracking-tight">Telekonsultasi Video</h1>
            <p class="text-xs sm:text-sm text-slate-500 font-normal mt-0.5">
                Konsultasi jarak jauh dengan dokter faskes melalui video konferensi aman dan terintegrasi.
            </p>
        </div>

        <a href="{{ route('patient.reservasi.create') }}"
           class="px-4 py-2.5 bg-primary hover:bg-primary-hover text-white rounded-hospital text-xs font-semibold shadow-xs transition flex items-center gap-2 self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Jadwalkan Konsultasi Baru</span>
        </a>
    </div>

    <!-- Alert / Information Banner -->
    <div class="bg-blue-50 border border-blue-200/80 rounded-hospital p-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div class="text-xs text-blue-900 leading-relaxed">
            <span class="font-bold">Informasi Pelayanan Telekonsultasi:</span>
            Pastikan perangkat Anda memiliki kamera dan mikrofon yang berfungsi serta koneksi internet yang stabil.
            Ruang tunggu virtual dapat diakses sebelum waktu konsultasi dimulai. Untuk kegawatdaruratan medis, segera hubungi 119 atau kunjungi IGD terdekat.
        </div>
    </div>

    <!-- Teleconsultation List -->
    @if($teleconsultations->isNotEmpty())
        <div class="space-y-3">
            @foreach($teleconsultations as $tele)
                <div class="bg-white rounded-hospital border {{ in_array($tele->status, ['doctor_ready', 'in_progress']) ? 'border-emerald-500 ring-1 ring-emerald-500/20' : 'border-line' }} p-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-start sm:items-center gap-4">
                        <div class="w-12 h-12 rounded-hospital {{ in_array($tele->status, ['doctor_ready', 'in_progress']) ? 'bg-emerald-50 text-accent border border-emerald-200' : 'bg-primary-50 text-primary border border-primary/20' }} flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-bold text-ink text-sm sm:text-base">{{ $tele->service->name }}</h3>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $tele->status_badge_class }}">
                                    {{ $tele->status_label }}
                                </span>
                            </div>
                            <div class="text-xs text-slate-600 mt-1 flex flex-wrap items-center gap-x-3 gap-y-1">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span class="font-medium text-ink">{{ $tele->doctor?->name ?? 'Belum ada dokter penugasan' }}</span>
                                </span>
                                <span class="text-slate-300">&bull;</span>
                                <span class="font-mono text-slate-600 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $tele->scheduled_at ? $tele->scheduled_at->translatedFormat('d F Y, H:i') : '-' }} WIB
                                </span>
                                @if($tele->appointment)
                                    <span class="text-slate-300">&bull;</span>
                                    <span class="text-slate-400 font-mono text-[11px]">{{ $tele->appointment->appointment_number }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 self-end md:self-center shrink-0">
                        <a href="{{ route('patient.telekonsultasi.show', $tele) }}"
                           class="px-3.5 py-2 border border-line text-slate-700 hover:bg-surface rounded-hospital text-xs font-semibold transition-colors">
                            Lihat Detail
                        </a>

                        @if($tele->canPatientEnterRoom())
                            <a href="{{ route('patient.telekonsultasi.room', $tele) }}"
                               class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-hospital text-xs font-bold transition shadow-xs flex items-center gap-1.5 animate-pulse">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <span>Masuk Ruang Video</span>
                            </a>
                        @elseif($tele->canPatientAccessWaiting())
                            <a href="{{ route('patient.telekonsultasi.waiting', $tele) }}"
                               class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-hospital text-xs font-semibold transition shadow-xs flex items-center gap-1.5">
                                <span>Ruang Tunggu &rarr;</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach

            <div class="mt-4">
                {{ $teleconsultations->links() }}
            </div>
        </div>
    @else
        <div class="bg-white rounded-hospital p-12 text-center border border-line shadow-xs text-slate-400">
            <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            <p class="font-bold text-slate-700 text-base">Belum Ada Sesi Telekonsultasi</p>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                Anda belum memiliki jadwal telekonsultasi. Buat reservasi baru dan pilih metode "Telekonsultasi Video" untuk konsultasi online.
            </p>
            <a href="{{ route('patient.reservasi.create') }}" class="mt-4 inline-block px-4 py-2 bg-primary hover:bg-primary-hover text-white font-semibold text-xs rounded-hospital shadow-xs transition-colors">
                + Jadwalkan Telekonsultasi Baru
            </a>
        </div>
    @endif
</div>
@endsection
