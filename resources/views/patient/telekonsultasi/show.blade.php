@extends('patient.layouts.app')

@section('title', 'Persiapan Telekonsultasi — PuskesmasFlow')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Breadcrumb / Back Link -->
    <div>
        <a href="{{ route('patient.telekonsultasi.index') }}" class="text-xs font-semibold text-primary hover:text-primary-hover flex items-center gap-1">
            &larr; Kembali ke Daftar Telekonsultasi
        </a>
        <h1 class="text-xl sm:text-2xl font-extrabold text-ink tracking-tight mt-2">Detail & Persiapan Telekonsultasi</h1>
        <p class="text-xs sm:text-sm text-slate-500 font-normal mt-0.5">
            Baca ketentuan keselamatan, izin perangkat, dan berikan konfirmasi persetujuan sebelum bergabung.
        </p>
    </div>

    <!-- Emergency Safety Warning Banner (Requirement #7) -->
    <div class="bg-rose-50 border-2 border-rose-300 rounded-hospital p-5 shadow-xs">
        <div class="flex items-start gap-3">
            <div class="w-9 h-9 rounded-full bg-rose-100 flex items-center justify-center text-rose-700 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div class="text-xs text-rose-950 space-y-1">
                <span class="text-sm font-extrabold text-rose-800 uppercase tracking-wide block">PERINGATAN KESELAMATAN MEDIS:</span>
                <p class="leading-relaxed">
                    Layanan telekonsultasi ini ditujukan khusus untuk keluhan medis non-kritis dan tindak lanjut rawat jalan.
                    <span class="font-bold underline">Jika Anda atau pasien mengalami kondisi gawat darurat</span> (seperti sesak napas berat, nyeri dada kiri menjalar, penurunan kesadaran mendadak, kejang, atau perdarahan hebat), 
                    <span class="font-bold text-rose-900">JANGAN menunggu jadwal video call</span>. Segera hubungi nomor darurat <strong>119</strong> atau bawa pasien ke Instalasi Gawat Darurat (IGD) rumah sakit terdekat.
                </p>
            </div>
        </div>
    </div>

    <!-- Session Overview Card -->
    <div class="bg-white rounded-hospital border border-line p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-line">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Layanan Poliklinik</span>
                <span class="text-base font-bold text-ink">{{ $teleconsultation->service->name }}</span>
            </div>
            <span class="text-xs font-bold px-3 py-1 rounded-full border {{ $teleconsultation->status_badge_class }}">
                {{ $teleconsultation->status_label }}
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div>
                <span class="text-slate-400 block">Dokter Pemeriksa:</span>
                <span class="font-bold text-ink mt-0.5 block text-sm">{{ $teleconsultation->doctor?->name ?? 'Akan Ditugaskan oleh Faskes' }}</span>
            </div>
            <div>
                <span class="text-slate-400 block">Jadwal Sesi:</span>
                <span class="font-bold text-primary mt-0.5 block text-sm font-mono">
                    {{ $teleconsultation->scheduled_at ? $teleconsultation->scheduled_at->translatedFormat('l, d F Y — H:i') : '-' }} WIB
                </span>
            </div>
            <div>
                <span class="text-slate-400 block">Kode Reservasi:</span>
                <span class="font-mono text-ink mt-0.5 block">{{ $teleconsultation->appointment?->appointment_number ?? '-' }}</span>
            </div>
            <div>
                <span class="text-slate-400 block">Nama Pasien:</span>
                <span class="font-semibold text-ink mt-0.5 block">{{ $patient->name }} ({{ $patient->masked_nik }})</span>
            </div>
        </div>

        @if($teleconsultation->appointment?->notes)
            <div class="pt-3 border-t border-line text-xs">
                <span class="text-slate-400 block">Keluhan / Catatan Awal:</span>
                <p class="text-slate-700 bg-surface p-3 rounded-hospital border border-line mt-1">{{ $teleconsultation->appointment->notes }}</p>
            </div>
        @endif

        @if($teleconsultation->clinical_notes)
            <div class="pt-3 border-t border-line text-xs">
                <span class="text-emerald-700 font-bold block">Ringkasan Medis / Catatan Dokter:</span>
                <p class="text-slate-800 bg-emerald-50/70 p-3 rounded-hospital border border-emerald-200 mt-1 whitespace-pre-line">{{ $teleconsultation->clinical_notes }}</p>
                @if($teleconsultation->disposition)
                    <p class="text-[11px] text-slate-500 mt-1.5">Tindak Lanjut / Disposisi: <span class="font-semibold text-ink capitalize">{{ str_replace('_', ' ', $teleconsultation->disposition) }}</span></p>
                @endif
            </div>
        @endif
    </div>

    <!-- Requirements & Privacy Checklist (Requirement #7) -->
    <div class="bg-surface rounded-hospital border border-line p-5 text-xs space-y-3">
        <h3 class="font-bold text-ink text-sm flex items-center gap-2">
            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            Petunjuk & Informasi Privasi Telemedis
        </h3>
        <ul class="space-y-2 text-slate-600 list-disc list-inside leading-relaxed">
            <li>Konsultasi ini menggunakan <strong>kamera dan mikrofon perangkat</strong> Anda. Harap berikan izin (Allow) saat peramban memintanya.</li>
            <li>Diperlukan <strong>koneksi internet yang stabil</strong> dengan kuota data yang memadai untuk audio-video berkualitas baik.</li>
            <li>Aliran data audio dan video diproses melalui infrastruktur komunikasi video untuk tujuan penegakan konsultasi medis.</li>
            <li>Aplikasi PuskesmasFlow <strong>tidak melakukan perekaman otomatis</strong> terhadap video atau audio percakapan konsultasi Anda.</li>
            <li>Telekonsultasi tidak selalu dapat menggantikan pemeriksaan fisik langsung. Apabila dokter menilai kondisi membutuhkan pemeriksaan fisik, laboratorium, atau penanganan lanjutan, Anda akan dirujuk ke faskes secara langsung.</li>
        </ul>
    </div>

    <!-- Consent Form & Action Section -->
    @if(! $teleconsultation->isCompleted() && ! $teleconsultation->isCancelled())
        <div class="bg-white rounded-hospital border border-line p-6 shadow-xs">
            <form method="POST" action="{{ route('patient.telekonsultasi.consent', $teleconsultation) }}" class="space-y-4">
                @csrf

                <label class="flex items-start gap-3 cursor-pointer select-none">
                    <input type="checkbox" name="consent" value="1" {{ $teleconsultation->patient_consent_given ? 'checked' : '' }}
                           class="w-4 h-4 text-primary rounded border-line focus:ring-primary mt-0.5">
                    <span class="text-xs text-slate-700 leading-relaxed">
                        Saya telah membaca, memahami, dan <strong>menyetujui ketentuan privasi serta batasan telekonsultasi</strong> medis di atas, serta bersedia memberikan akses kamera dan mikrofon perangkat untuk konsultasi ini.
                    </span>
                </label>
                @error('consent')
                    <p class="text-xs text-red-600 font-semibold">{{ $message }}</p>
                @enderror

                <div class="pt-2 flex flex-col sm:flex-row gap-3">
                    @if($teleconsultation->patient_consent_given && $teleconsultation->canPatientEnterRoom())
                        <a href="{{ route('patient.telekonsultasi.room', $teleconsultation) }}"
                           class="flex-1 py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-hospital font-bold text-xs text-center transition shadow-xs flex items-center justify-center gap-2 animate-pulse">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <span>Masuk Ruang Konsultasi (Dokter Siap)</span>
                        </a>
                    @else
                        <button type="submit"
                                class="flex-1 py-3 px-4 bg-primary hover:bg-primary-hover text-white rounded-hospital font-bold text-xs transition shadow-xs flex items-center justify-center gap-2">
                            <span>Konfirmasi Persetujuan & Masuk Ruang Tunggu &rarr;</span>
                        </button>
                    @endif

                    @if($teleconsultation->patient_consent_given)
                        <a href="{{ route('patient.telekonsultasi.waiting', $teleconsultation) }}"
                           class="py-3 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-hospital font-semibold text-xs text-center transition">
                            Buka Ruang Tunggu
                        </a>
                    @endif
                </div>
            </form>
        </div>
    @endif
</div>
@endsection
