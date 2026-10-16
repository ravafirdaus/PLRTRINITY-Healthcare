<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="font-extrabold text-xl sm:text-2xl text-ink tracking-tight">
                    {{ __('Laporan Operasional & Kinerja Pelayanan') }}
                </h1>
                <p class="text-xs text-slate-500 font-normal mt-0.5">
                    Metrik dan data operasional faskes dihitung real-time dari database.
                </p>
            </div>

            <!-- Filter Date Range Form -->
            <form action="{{ route('reports.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                <input type="date" name="start_date" value="{{ $startDate }}"
                       class="rounded-hospital border-line bg-white text-xs font-mono focus:border-primary focus:ring-1 focus:ring-primary">
                <span class="text-xs text-slate-400">s/d</span>
                <input type="date" name="end_date" value="{{ $endDate }}"
                       class="rounded-hospital border-line bg-white text-xs font-mono focus:border-primary focus:ring-1 focus:ring-primary">
                <button type="submit" class="px-3.5 py-2 bg-primary hover:bg-primary-hover text-white font-semibold text-xs rounded-hospital shadow-xs transition-colors">
                    Filter Laporan
                </button>
            </form>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Summary Stat Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white rounded-hospital p-5 border border-line shadow-xs">
                <span class="text-xs text-slate-500 font-medium uppercase tracking-wider">Total Pasien</span>
                <div class="text-2xl sm:text-3xl font-extrabold text-ink mt-1">{{ $report['total_patients'] }}</div>
                <span class="text-[11px] text-slate-400">Seluruh pendaftaran</span>
            </div>

            <div class="bg-white rounded-hospital p-5 border border-line shadow-xs">
                <span class="text-xs text-slate-500 font-medium uppercase tracking-wider">Selesai Dilayani</span>
                <div class="text-2xl sm:text-3xl font-extrabold text-accent mt-1">{{ $report['completed_patients'] }}</div>
                <span class="text-[11px] text-accent font-semibold">Tuntas diperiksa</span>
            </div>

            <div class="bg-white rounded-hospital p-5 border border-line shadow-xs">
                <span class="text-xs text-slate-500 font-medium uppercase tracking-wider">Sedang Menunggu</span>
                <div class="text-2xl sm:text-3xl font-extrabold text-amber-600 mt-1">{{ $report['waiting_patients'] }}</div>
                <span class="text-[11px] text-amber-700 font-semibold">Triase & poli</span>
            </div>

            <div class="bg-white rounded-hospital p-5 border border-line shadow-xs">
                <span class="text-xs text-slate-500 font-medium uppercase tracking-wider">Dilewati / Batal</span>
                <div class="text-2xl sm:text-3xl font-extrabold text-slate-600 mt-1">{{ $report['skipped_patients'] + $report['cancelled_patients'] }}</div>
                <span class="text-[11px] text-slate-400">{{ $report['skipped_patients'] }} lewat &bull; {{ $report['cancelled_patients'] }} batal</span>
            </div>

            <div class="bg-white rounded-hospital p-5 border border-line shadow-xs col-span-2 lg:col-span-1">
                <span class="text-xs text-slate-500 font-medium uppercase tracking-wider">Rata-rata Konsultasi</span>
                <div class="text-2xl sm:text-3xl font-extrabold text-primary mt-1">{{ $report['avg_duration_minutes'] }} <span class="text-xs font-normal text-slate-500">mnt</span></div>
                <span class="text-[11px] text-slate-400">Durasi aktual dokter</span>
            </div>
        </div>

        <!-- Breakdown Grids -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Jenis Pendaftaran -->
            <div class="bg-white rounded-hospital p-6 border border-line shadow-xs">
                <h3 class="text-sm font-bold text-ink uppercase tracking-wider mb-4 pb-2 border-b border-line">
                    Jalur Kedatangan Pasien
                </h3>
                <div class="space-y-4 text-xs font-semibold">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                            Reservasi Terjadwal Online:
                        </span>
                        <span class="font-mono font-bold text-ink text-sm">{{ $report['by_reg_type']['reservation'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-accent"></span>
                            Walk-in (Datang Langsung):
                        </span>
                        <span class="font-mono font-bold text-ink text-sm">{{ $report['by_reg_type']['walk_in'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                            Darurat (Jalur Segera):
                        </span>
                        <span class="font-mono font-bold text-red-600 text-sm">{{ $report['by_reg_type']['emergency'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Distribusi Triase -->
            <div class="bg-white rounded-hospital p-6 border border-line shadow-xs">
                <h3 class="text-sm font-bold text-ink uppercase tracking-wider mb-4 pb-2 border-b border-line">
                    Distribusi Kategori Triase (ATS)
                </h3>
                <div class="space-y-4 text-xs font-semibold">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-600"></span>
                            Merah (Resusitasi/Darurat):
                        </span>
                        <span class="font-mono font-bold text-red-600 text-sm">{{ $report['triage']['merah'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            Kuning (Mendesak/Urgent):
                        </span>
                        <span class="font-mono font-bold text-amber-600 text-sm">{{ $report['triage']['kuning'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-accent"></span>
                            Hijau (Tidak Mendesak):
                        </span>
                        <span class="font-mono font-bold text-accent text-sm">{{ $report['triage']['hijau'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-800"></span>
                            Hitam (Meninggal):
                        </span>
                        <span class="font-mono font-bold text-slate-800 text-sm">{{ $report['triage']['hitam'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Distribusi per Poli -->
            <div class="bg-white rounded-hospital p-6 border border-line shadow-xs">
                <h3 class="text-sm font-bold text-ink uppercase tracking-wider mb-4 pb-2 border-b border-line">
                    Kunjungan per Poli Layanan
                </h3>
                <div class="space-y-3 text-xs font-semibold">
                    @foreach($report['services'] as $svc)
                        <div class="flex items-center justify-between">
                            <span class="text-slate-700">{{ $svc->name }} ({{ $svc->code }})</span>
                            <span class="font-mono font-bold text-ink text-sm">{{ $svc->queue_entries_count }} pasien</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Consultation Sessions Table -->
        <div class="space-y-3">
            <h3 class="text-base font-bold text-ink">
                Sesi Konsultasi Medis Terkini
            </h3>

            <div class="bg-white rounded-hospital border border-line shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-surface border-b border-line text-slate-600 font-semibold uppercase tracking-wider">
                            <tr>
                                <th class="p-3.5">Pasien & Antrean</th>
                                <th class="p-3.5">Poli & Dokter</th>
                                <th class="p-3.5">Waktu Mulai - Selesai</th>
                                <th class="p-3.5">Durasi Pelayanan</th>
                                <th class="p-3.5">Disposisi Pasien</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line font-medium text-slate-700">
                            @forelse($report['recent_sessions'] as $session)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="p-3.5">
                                        <div class="font-bold text-ink text-sm">{{ $session->queueEntry->patient->name }}</div>
                                        <div class="font-mono text-primary text-[11px]">{{ $session->queueEntry->queue_code }}</div>
                                    </td>
                                    <td class="p-3.5">
                                        <div class="font-semibold text-slate-800">{{ $session->service->name }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $session->doctor->name }}</div>
                                    </td>
                                    <td class="p-3.5 font-mono text-slate-600">
                                        {{ $session->started_at->format('H:i') }} - {{ $session->ended_at ? $session->ended_at->format('H:i') : '-' }} WIB
                                    </td>
                                    <td class="p-3.5 font-mono text-ink font-bold">
                                        {{ $session->duration_minutes }} Menit
                                    </td>
                                    <td class="p-3.5">
                                        <span class="inline-block px-2.5 py-1 rounded-hospital bg-surface border border-line text-slate-700 text-[10px] font-bold uppercase">
                                            {{ $session->disposition_label }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-slate-400">
                                        Belum ada sesi konsultasi yang selesai pada periode ini.
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
