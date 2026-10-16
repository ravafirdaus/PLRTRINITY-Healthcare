<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-ink tracking-tight">
                    {{ __('Meja Penilaian Triase Klinis') }}
                </h1>
                <p class="text-xs text-ink-muted mt-0.5">
                    {{ $todayDate }} &bull; Standar Penilaian Prioritas Klinis (PMK No. 47/2018)
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 rounded-hospital bg-amber-50 text-amber-800 border border-amber-200 text-xs font-bold flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    Menunggu Triase: {{ count($waitingTriage) }} Pasien
                </span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        <!-- Banner Standar Triase Klinis (Clean Light Hospital Notice) -->
        <div class="bg-surface-subtle rounded-hospital p-5 sm:p-6 border border-line flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-2xs">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-hospital bg-primary/10 text-primary text-[10px] font-bold uppercase tracking-wider border border-primary/20">
                        Standar Triase Nasional (ATS)
                    </span>
                    <span class="text-xs font-semibold text-accent">• Keputusan Klinis Berwenang</span>
                </div>
                <h3 class="text-base font-bold text-ink">Penilaian Berdasarkan Tanda Vital & Anamnesis Klinis</h3>
                <p class="text-xs text-ink-muted max-w-2xl leading-relaxed">
                    Petugas kesehatan memeriksa tanda-tanda vital (tekanan darah, nadi, respirasi, SpO2, suhu) dan keluhan utama sebelum menetapkan kategori operasional.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 text-xs font-bold shrink-0">
                <span class="px-2.5 py-1 rounded-hospital bg-rose-50 text-rose-700 border border-rose-200">Merah: Darurat (P1)</span>
                <span class="px-2.5 py-1 rounded-hospital bg-amber-50 text-amber-700 border border-amber-200">Kuning: Urgent (P2)</span>
                <span class="px-2.5 py-1 rounded-hospital bg-emerald-50 text-emerald-700 border border-emerald-200">Hijau: Biasa (P3)</span>
            </div>
        </div>

        <!-- Bagian 1: Antrean Menunggu Triase -->
        <div>
            <div class="flex items-center justify-between mb-3 px-1">
                <h2 class="text-sm font-bold text-ink flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    <span>Daftar Pasien Menunggu Penilaian Triase ({{ count($waitingTriage) }})</span>
                </h2>
                <span class="text-xs text-ink-muted">Urut berdasarkan waktu pendaftaran</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($waitingTriage as $q)
                    <div class="bg-white rounded-hospital p-5 border border-line hover:border-primary/50 shadow-card transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2.5 pb-2.5 border-b border-line">
                                <span class="px-2.5 py-1 rounded-hospital bg-surface-subtle text-primary border border-line font-mono font-bold text-sm">
                                    {{ $q->queue_code }}
                                </span>
                                <span class="text-xs text-ink-muted font-mono">{{ $q->created_at->format('H:i') }} WIB</span>
                            </div>

                            <div class="font-bold text-base text-ink leading-snug">{{ $q->patient->name }}</div>
                            <div class="text-xs text-ink-muted font-mono mt-0.5">
                                NIK: {{ $q->patient->nik }} &bull; Usia: {{ $q->patient->age }} th
                            </div>
                            <div class="text-xs font-semibold text-accent mt-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span>Poli Tujuan: {{ $q->service->name }}</span>
                            </div>
                            @if($q->appointment && $q->appointment->notes)
                                <div class="mt-3 p-2.5 rounded-hospital bg-surface-subtle text-xs text-ink-muted border border-line">
                                    <strong class="text-ink">Keluhan Reservasi:</strong> {{ $q->appointment->notes }}
                                </div>
                            @endif
                        </div>

                        <div class="mt-5 pt-3.5 border-t border-line">
                            <a href="{{ route('triage.create', $q->id) }}" class="w-full h-10 px-4 bg-primary hover:bg-primary-hover text-white text-xs font-semibold rounded-hospital transition-colors shadow-xs text-center flex items-center justify-center gap-2">
                                <span>Lakukan Penilaian Triase</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-hospital p-12 text-center border border-line text-ink-muted text-xs shadow-card">
                        <svg class="w-10 h-10 text-line mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="font-bold text-sm text-ink">Tidak ada pasien yang menunggu penilaian triase saat ini.</p>
                        <p class="mt-0.5">Semua antrean yang terdaftar telah dinilai dan diteruskan ke ruang poli.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Bagian 2: Riwayat Penilaian Triase Hari Ini -->
        <div>
            <div class="flex items-center justify-between mb-3 px-1">
                <h2 class="text-sm font-bold text-ink flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <span>Riwayat Penilaian Triase Hari Ini ({{ count($assessedToday) }})</span>
                </h2>
                <span class="text-xs text-ink-muted">Telah dinilai oleh petugas</span>
            </div>

            <div class="bg-white rounded-hospital border border-line shadow-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-line bg-surface-subtle text-ink font-semibold">
                                <th class="py-3 px-4">No. Antrean & Pasien</th>
                                <th class="py-3 px-4 text-center">Kategori Triase</th>
                                <th class="py-3 px-4">Tanda Vital Utama</th>
                                <th class="py-3 px-4">Keluhan Utama</th>
                                <th class="py-3 px-4">Petugas Penilai</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line font-normal text-ink">
                            @forelse($assessedToday as $a)
                                <tr class="hover:bg-surface-subtle/50 transition-colors">
                                    <td class="py-3.5 px-4">
                                        <div class="font-mono font-bold text-primary text-sm">{{ $a->queueEntry->queue_code }}</div>
                                        <div class="font-bold text-ink">{{ $a->queueEntry->patient->name }}</div>
                                        <div class="text-[11px] text-accent font-semibold">{{ $a->queueEntry->service->name }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-hospital text-[11px] font-bold uppercase
                                            {{ match($a->category) {
                                                'merah' => 'bg-rose-50 text-rose-700 border border-rose-200',
                                                'kuning' => 'bg-amber-50 text-amber-700 border border-amber-200',
                                                'hijau' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                                'hitam' => 'bg-slate-100 text-slate-800 border border-slate-300',
                                                default => 'bg-surface-subtle text-ink border border-line'
                                            } }}">
                                            {{ $a->category }}
                                            @if($a->is_immediate_response_triggered)
                                                (DARURAT)
                                            @endif
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-[11px] text-ink-muted">
                                        TD: {{ $a->systolic_bp && $a->diastolic_bp ? "{$a->systolic_bp}/{$a->diastolic_bp}" : '-' }} mmHg &bull;
                                        HR: {{ $a->heart_rate ? "{$a->heart_rate} bpm" : '-' }} &bull;
                                        Suhu: {{ $a->temperature ? "{$a->temperature}°C" : '-' }} &bull;
                                        SpO2: {{ $a->oxygen_saturation ? "{$a->oxygen_saturation}%" : '-' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-ink-muted max-w-xs truncate">
                                        {{ $a->chief_complaint }}
                                    </td>
                                    <td class="py-3.5 px-4 text-ink-muted">
                                        <div class="font-semibold text-ink">{{ $a->assessedBy->name }}</div>
                                        <div class="text-[10px] text-ink-muted font-mono">{{ $a->assessed_at->format('H:i') }} WIB</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <a href="{{ route('triage.create', $a->queue_entry_id) }}" class="inline-flex items-center px-2.5 py-1 bg-surface-subtle hover:bg-slate-100 text-ink font-semibold text-[11px] rounded-hospital border border-line transition-colors">
                                            Ubah Penilaian
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-ink-muted text-xs">
                                        Belum ada penilaian triase yang dicatat hari ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
