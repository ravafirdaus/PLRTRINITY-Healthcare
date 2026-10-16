<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('doctor.telekonsultasi.index') }}" class="text-xs font-semibold text-primary hover:text-primary-hover flex items-center gap-1 mb-1">
                    &larr; Kembali ke Daftar Telekonsultasi
                </a>
                <h1 class="text-xl sm:text-2xl font-extrabold text-ink tracking-tight">
                    {{ __('Detail Pasien Telekonsultasi') }}
                </h1>
            </div>

            <!-- Header Action Button -->
            <div class="flex items-center gap-2">
                @if($teleconsultation->status === 'scheduled' || $teleconsultation->status === 'waiting_doctor')
                    <form method="POST" action="{{ route('doctor.telekonsultasi.start', $teleconsultation) }}">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-hospital text-xs font-bold transition shadow-xs flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Mulai Sesi Sekarang</span>
                        </button>
                    </form>
                @elseif($teleconsultation->status === 'doctor_ready' || $teleconsultation->status === 'in_progress')
                    <a href="{{ route('doctor.telekonsultasi.room', $teleconsultation) }}"
                       class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-hospital text-xs font-bold transition shadow-xs flex items-center gap-1.5 animate-pulse">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span>Masuk Ruang Video</span>
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl space-y-6">
        <!-- Patient Identity & Medical Card -->
        <div class="bg-white rounded-hospital border border-line p-6 shadow-xs space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-line">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-primary-50 text-primary font-extrabold text-base flex items-center justify-center border border-primary/20">
                        {{ strtoupper(substr($teleconsultation->patient->name, 0, 2)) }}
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-ink">{{ $teleconsultation->patient->name }}</h2>
                        <span class="text-xs text-slate-500 font-mono">NIK: {{ $teleconsultation->patient->masked_nik }} &bull; {{ $teleconsultation->patient->gender_label }}, {{ $teleconsultation->patient->age }} th</span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold px-3 py-1 rounded-full border {{ $teleconsultation->status_badge_class }}">
                        {{ $teleconsultation->status_label }}
                    </span>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <span class="text-slate-400 block">Poli Tujuan:</span>
                    <span class="font-bold text-ink mt-0.5 block text-sm">{{ $teleconsultation->service->name }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block">Jadwal Sesi:</span>
                    <span class="font-bold text-primary mt-0.5 block font-mono text-sm">
                        {{ $teleconsultation->scheduled_at ? $teleconsultation->scheduled_at->translatedFormat('d M Y, H:i') : '-' }} WIB
                    </span>
                </div>
                <div>
                    <span class="text-slate-400 block">Kode Reservasi:</span>
                    <span class="font-mono text-ink mt-0.5 block">{{ $teleconsultation->appointment?->appointment_number ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block">No. Telepon Pasien:</span>
                    <span class="font-medium text-ink mt-0.5 block font-mono">{{ $teleconsultation->patient->phone ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block">Nomor BPJS:</span>
                    <span class="font-medium text-ink mt-0.5 block font-mono">{{ $teleconsultation->patient->bpjs_number ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block">Persetujuan Pasien:</span>
                    @if($teleconsultation->patient_consent_given)
                        <span class="text-emerald-700 font-bold mt-0.5 block flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Telah Menyetujui
                        </span>
                    @else
                        <span class="text-amber-700 font-semibold mt-0.5 block">Menunggu Konfirmasi di Ruang Tunggu</span>
                    @endif
                </div>
            </div>

            <!-- Notes Section -->
            @if($teleconsultation->appointment?->notes)
                <div class="pt-4 border-t border-line text-xs">
                    <span class="text-slate-400 font-bold uppercase tracking-wider block text-[10px]">Catatan / Keluhan Awal Pasien:</span>
                    <p class="text-slate-700 bg-surface p-3 rounded-hospital border border-line mt-1 leading-relaxed">
                        {{ $teleconsultation->appointment->notes }}
                    </p>
                </div>
            @endif

            <!-- Clinical Record Summary if completed -->
            @if($teleconsultation->isCompleted())
                <div class="pt-4 border-t border-line space-y-3">
                    <div class="bg-emerald-50/80 border border-emerald-200 rounded-hospital p-4 text-xs space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-emerald-900 text-sm flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Rekam Konsultasi Telemedis (Selesai)
                            </span>
                            <span class="text-slate-500 font-mono text-[11px]">Durasi: {{ $teleconsultation->duration_minutes ?? 0 }} menit</span>
                        </div>

                        <div>
                            <span class="font-bold text-slate-700 block">Catatan Klinis Dokter:</span>
                            <p class="text-slate-800 mt-1 whitespace-pre-line bg-white p-3 rounded border border-emerald-100">{{ $teleconsultation->clinical_notes ?? 'Tidak ada catatan tambahan' }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-[11px] pt-1">
                            <div>
                                <span class="text-slate-500">Tindak Lanjut / Disposisi:</span>
                                <span class="font-bold text-ink uppercase block mt-0.5">{{ str_replace('_', ' ', $teleconsultation->disposition ?? 'pulang') }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500">Pemeriksa / Selesai Oleh:</span>
                                <span class="font-semibold text-ink block mt-0.5">{{ $teleconsultation->completedByUser?->name ?? $teleconsultation->doctor?->name }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
