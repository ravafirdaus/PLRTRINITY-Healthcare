@extends('patient.layouts.app')

@section('title', 'Riwayat Reservasi — PuskesmasFlow')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-ink tracking-tight">Riwayat Reservasi Pasien</h1>
            <p class="text-xs sm:text-sm text-slate-500 font-normal mt-0.5">Daftar semua jadwal kunjungan pelayanan kesehatan Anda di faskes.</p>
        </div>
        <a href="{{ route('patient.reservasi.create') }}" class="inline-flex items-center justify-center bg-primary hover:bg-primary-hover text-white font-semibold py-2 px-3.5 rounded-hospital text-xs sm:text-sm transition-colors shadow-xs">
            <span class="mr-1">+</span> Buat Reservasi
        </a>
    </div>

    @if($appointments->isEmpty())
        <div class="bg-white rounded-hospital border border-line shadow-xs p-8 text-center">
            <div class="w-12 h-12 rounded-hospital bg-surface flex items-center justify-center text-slate-400 mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <h3 class="text-sm font-bold text-ink">Belum Ada Riwayat Reservasi</h3>
            <p class="text-xs text-slate-500 mt-1 mb-5">Anda belum pernah membuat reservasi pelayanan kesehatan.</p>
            <a href="{{ route('patient.reservasi.create') }}" class="inline-block bg-primary hover:bg-primary-hover text-white font-semibold py-2 px-4 rounded-hospital text-xs transition-colors shadow-xs">
                Buat Reservasi Sekarang
            </a>
        </div>
    @else
        <div class="bg-white rounded-hospital border border-line shadow-xs overflow-hidden">
            <div class="divide-y divide-line">
                @foreach($appointments as $appt)
                    <div class="p-4 sm:p-5 hover:bg-slate-50/80 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-bold text-ink text-sm sm:text-base">Poli {{ $appt->service->name }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold
                                    @if($appt->status === 'confirmed' || $appt->status === 'booked') bg-amber-50 text-amber-700 border border-amber-200
                                    @elseif($appt->status === 'checked_in') bg-emerald-50 text-accent border border-emerald-200
                                    @elseif($appt->status === 'completed') bg-sky-50 text-primary border border-sky-200
                                    @elseif($appt->status === 'cancelled') bg-red-50 text-red-600 border border-red-200
                                    @elseif($appt->status === 'no_show') bg-surface text-slate-600 border border-line
                                    @else bg-surface text-slate-800
                                    @endif">
                                    @if($appt->status === 'confirmed' || $appt->status === 'booked') Menunggu Check-in
                                    @elseif($appt->status === 'checked_in') Sudah Check-in
                                    @elseif($appt->status === 'completed') Selesai
                                    @elseif($appt->status === 'cancelled') Dibatalkan
                                    @elseif($appt->status === 'no_show') Tidak Hadir
                                    @else {{ ucfirst($appt->status) }}
                                    @endif
                                </span>
                            </div>
                            <p class="text-xs text-slate-600">
                                📅 {{ \Carbon\Carbon::parse($appt->appointment_date)->isoFormat('dddd, D MMMM Y') }}
                                &bull; ⏰ {{ \Carbon\Carbon::parse($appt->slot_time)->format('H:i') }} WIB
                            </p>
                            <p class="text-[11px] text-slate-400 font-mono">
                                Kode: {{ $appt->reservation_code ?? $appt->appointment_number }}
                            </p>
                            @if($appt->notes)
                                <p class="text-[11px] text-slate-500 italic mt-1">"{{ \Illuminate\Support\Str::limit($appt->notes, 80) }}"</p>
                            @endif
                        </div>

                        <div class="flex items-center space-x-3 self-end sm:self-center">
                            <a href="{{ route('patient.reservasi.show', $appt) }}" 
                               class="text-primary hover:text-primary-hover text-xs font-semibold">
                                Detail Tiket &rarr;
                            </a>
                            @if($appt->status === 'confirmed' || $appt->status === 'booked')
                                <form method="POST" action="{{ route('patient.reservasi.cancel', $appt) }}" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin membatalkan reservasi ini?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">
                                        Batalkan
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if($appointments->hasPages())
                <div class="p-3.5 border-t border-line bg-surface">
                    {{ $appointments->links() }}
                </div>
            @endif
        </div>
    @endif

    <div class="text-center pt-2">
        <a href="{{ route('patient.dashboard') }}" class="text-xs font-semibold text-slate-500 hover:text-ink">
            &larr; Kembali ke Dashboard Pasien
        </a>
    </div>
</div>
@endsection
