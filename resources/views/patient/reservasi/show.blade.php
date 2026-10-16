@extends('patient.layouts.app')

@section('title', 'Detail Reservasi — PuskesmasFlow')

@section('content')
<div class="max-w-lg mx-auto space-y-4">
    <div class="bg-white rounded-hospital border border-line shadow-xs overflow-hidden">
        {{-- Header Bar --}}
        <div class="bg-primary p-6 text-center text-white">
            <span class="text-[10px] font-bold uppercase tracking-widest text-primary-50">BUKTI RESERVASI FASKES</span>
            <h1 class="text-lg font-bold mt-1">Detail Reservasi Pasien</h1>
            <p class="text-xs text-primary-100 mt-0.5">Tunjukkan kode berikut kepada petugas loket saat check-in</p>
            <div class="mt-3 bg-white/15 backdrop-blur-xs rounded-hospital py-2.5 px-5 inline-block border border-white/20 select-all">
                <span class="text-2xl sm:text-3xl font-mono font-extrabold tracking-widest">{{ $appointment->reservation_code ?? $appointment->appointment_number }}</span>
            </div>
        </div>

        {{-- Details --}}
        <div class="p-6 space-y-4 text-xs">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-slate-400">Nama Pasien</p>
                    <p class="font-bold text-ink mt-0.5 text-sm">{{ $patient->name }}</p>
                </div>
                <div>
                    <p class="text-slate-400">NIK Pasien</p>
                    <p class="font-medium text-ink mt-0.5 font-mono">{{ $patient->masked_nik }}</p>
                </div>
                <div>
                    <p class="text-slate-400">Layanan Poli</p>
                    <p class="font-bold text-ink mt-0.5">{{ $appointment->service->name }}</p>
                </div>
                <div>
                    <p class="text-slate-400">Metode Pelayanan</p>
                    <div class="mt-0.5">
                        @if($appointment->isVideo())
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-primary-50 text-primary border border-primary/20">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                Telekonsultasi Video
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                Tatap Muka di Puskesmas
                            </span>
                        @endif
                    </div>
                </div>
                <div>
                    <p class="text-slate-400">Status</p>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold mt-0.5
                        {{ ($appointment->status === 'confirmed' || $appointment->status === 'booked') ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                        {{ $appointment->status === 'checked_in' ? 'bg-emerald-50 text-accent border border-emerald-200' : '' }}
                        {{ $appointment->status === 'completed' ? 'bg-sky-50 text-primary border border-sky-200' : '' }}
                        {{ $appointment->status === 'cancelled' ? 'bg-red-50 text-red-600 border border-red-200' : '' }}">
                        {{ ($appointment->status === 'confirmed' || $appointment->status === 'booked') ? 'Terkonfirmasi' : '' }}
                        {{ $appointment->status === 'checked_in' ? 'Sudah Check-in' : '' }}
                        {{ $appointment->status === 'completed' ? 'Selesai' : '' }}
                        {{ $appointment->status === 'cancelled' ? 'Dibatalkan' : '' }}
                    </span>
                </div>
                <div>
                    <p class="text-slate-400">Tanggal Kunjungan</p>
                    <p class="font-medium text-ink mt-0.5">{{ \Carbon\Carbon::parse($appointment->appointment_date)->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
                <div>
                    <p class="text-slate-400">Slot Waktu</p>
                    <p class="font-bold text-primary mt-0.5 font-mono text-sm">{{ \Carbon\Carbon::parse($appointment->slot_time)->format('H:i') }} WIB</p>
                </div>
                @if($appointment->teleconsultation && $appointment->teleconsultation->doctor)
                <div>
                    <p class="text-slate-400">Dokter Penanggung Jawab</p>
                    <p class="font-bold text-ink mt-0.5">{{ $appointment->teleconsultation->doctor->name }}</p>
                </div>
                @endif
            </div>

            {{-- Transparansi Subsidi & Status Biaya --}}
            <div class="rounded-hospital p-4 border {{ $patient->is_subsidized ? 'bg-emerald-50/70 border-emerald-200 text-emerald-900' : 'bg-sky-50/70 border-sky-200 text-sky-900' }} space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-[11px] uppercase tracking-wider {{ $patient->is_subsidized ? 'text-emerald-800' : 'text-sky-800' }}">
                        Transparansi Subsidi &amp; Biaya (e-KIP)
                    </span>
                    @if($patient->is_subsidized)
                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            Bebas Biaya (Rp 0)
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-100 text-sky-800 border border-sky-300">
                            Mandiri / Reguler
                        </span>
                    @endif
                </div>

                @if($patient->is_subsidized)
                    <p class="text-[11px] text-emerald-800 leading-snug">
                        Status <strong>"Bebas Biaya (Rp 0)"</strong> — Sesuai kartu e-KIP Anda, Anda tidak dikenakan iuran bulanan dan bebas dari segala tagihan saat berkunjung ke faskes.
                    </p>
                @else
                    <div class="text-[11px] text-slate-700 space-y-0.5 pt-1">
                        <p class="font-semibold text-slate-800">Estimasi Rincian Biaya: Rp 25.000 (Pendaftaran Rp 10.000 + Konsultasi Rp 15.000)</p>
                        <p class="text-[10px] text-slate-500">Opsi pembayaran dapat dilakukan <em>on-the-spot</em> di loket Puskesmas (Tunai/Debit) atau Digital (QRIS).</p>
                    </div>
                @endif
            </div>

            @if($appointment->notes)
            <div class="pt-3 border-t border-line">
                <p class="text-slate-400">Catatan / Keluhan</p>
                <p class="text-slate-700 mt-1 bg-surface p-3 rounded-hospital border border-line">{{ $appointment->notes }}</p>
            </div>
            @endif

            @if($appointment->teleconsultation)
            <div class="bg-gradient-to-br from-primary-50 to-sky-50 rounded-hospital p-4 border border-primary/20 mt-3">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-primary flex items-center gap-1.5 uppercase tracking-wider">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Sesi Telekonsultasi Online
                    </span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $appointment->teleconsultation->status_badge_class }}">
                        {{ $appointment->teleconsultation->status_label }}
                    </span>
                </div>
                <p class="text-xs text-slate-600 mb-3">
                    Konsultasi jarak jauh akan berlangsung langsung di peramban web Anda tanpa aplikasi tambahan.
                </p>
                @if($appointment->teleconsultation->canPatientEnterRoom())
                    <a href="{{ route('patient.telekonsultasi.room', $appointment->teleconsultation) }}" 
                       class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-hospital bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-sm animate-pulse">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Masuk Ruang Konsultasi (Dokter Siap) &rarr;
                    </a>
                @elseif($appointment->teleconsultation->isCompleted())
                    <p class="text-xs font-semibold text-emerald-800 bg-emerald-50 p-2 rounded border border-emerald-200">
                        Sesi konsultasi ini telah selesai.
                    </p>
                @elseif($appointment->teleconsultation->isCancelled())
                    <p class="text-xs font-semibold text-red-700 bg-red-50 p-2 rounded border border-red-200">
                        Sesi konsultasi ini telah dibatalkan.
                    </p>
                @else
                    <a href="{{ route('patient.telekonsultasi.show', $appointment->teleconsultation) }}" 
                       class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-hospital bg-primary hover:bg-primary-hover text-white font-semibold text-xs transition shadow-xs">
                        Buka Ruang Persiapan & Tunggu &rarr;
                    </a>
                @endif
            </div>
            @endif

            @if($appointment->queueEntry)
            <div class="bg-primary-50 rounded-hospital p-4 border border-primary/20 mt-3 text-center">
                <p class="text-xs text-primary font-bold uppercase tracking-wider">Nomor Antrean Anda</p>
                <p class="text-3xl font-extrabold font-mono text-ink mt-1">{{ $appointment->queueEntry->queue_code ?? $appointment->queueEntry->queue_number }}</p>
                <p class="text-[11px] text-slate-500 mt-1">Status: <span class="font-semibold text-primary">{{ $appointment->queueEntry->status_label ?? ucfirst($appointment->queueEntry->status) }}</span></p>
            </div>
            @endif
        </div>

        {{-- Actions --}}
        <div class="bg-surface p-4 sm:p-5 flex flex-col sm:flex-row gap-3 border-t border-line">
            <a href="{{ route('patient.dashboard') }}" class="flex-1 text-center bg-primary hover:bg-primary-hover text-white font-semibold py-2 px-4 rounded-hospital transition-colors shadow-xs text-xs sm:text-sm">
                Kembali ke Dashboard
            </a>
            @if($appointment->status === 'confirmed' || $appointment->status === 'booked')
            <form method="POST" action="{{ route('patient.reservasi.cancel', $appointment) }}" class="flex-1"
                  onsubmit="return confirm('Yakin ingin membatalkan reservasi ini?')">
                @csrf
                @method('PATCH')
                <button type="submit" class="w-full bg-white hover:bg-red-50 border border-red-200 text-red-600 font-semibold py-2 px-4 rounded-hospital transition-colors shadow-xs text-xs sm:text-sm">
                    Batalkan Reservasi
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
