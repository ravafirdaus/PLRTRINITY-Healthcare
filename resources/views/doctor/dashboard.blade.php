<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-accent animate-pulse"></span>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-ink tracking-tight">
                        {{ __('Pelayanan Poli & Konsultasi Medis') }}
                    </h1>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 font-normal mt-0.5">
                    Kelola antrean pasien poliklinik dan catat rekam konsultasi rawat jalan.
                </p>
            </div>

            <!-- Header Right: Date/Time & Active Service Selector -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <!-- Date & Live Clock Widget -->
                <div class="bg-white px-3.5 py-2 rounded-hospital border border-line shadow-xs flex items-center gap-2.5"
                     x-data="{ time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB' }"
                     x-init="setInterval(() => { time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB' }, 1000)">
                    <div class="w-7 h-7 rounded-md bg-primary-50 flex items-center justify-center text-primary shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-[11px] font-medium text-slate-500">{{ $todayDate }}</div>
                        <div class="text-xs font-bold text-slate-800" x-text="time">10:28 WIB</div>
                    </div>
                </div>

                <!-- Active Service Selector -->
                <form action="{{ route('doctor.dashboard') }}" method="GET" class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-slate-500 hidden sm:inline">Poli Aktif:</span>
                    <div class="relative">
                        <select name="service_id" onchange="this.form.submit()"
                                class="py-2 pl-7 pr-8 text-xs font-bold rounded-hospital border border-line bg-white text-ink shadow-xs focus:border-primary focus:ring-1 focus:ring-primary cursor-pointer">
                            @foreach($services as $svc)
                                <option value="{{ $svc->id }}" {{ $activeService?->id === $svc->id ? 'selected' : '' }}>
                                    {{ $svc->name }} ({{ $svc->code }})
                                </option>
                            @endforeach
                        </select>
                        <span class="w-2.5 h-2.5 rounded-full bg-accent absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none"></span>
                    </div>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{ activeTab: '{{ $currentPatient ? 'examination' : 'waiting' }}' }">
        <!-- 4 Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Total Antrean -->
            <div class="bg-white p-5 rounded-hospital border border-line shadow-xs flex items-center justify-between">
                <div>
                    <div class="text-xs font-medium text-slate-500">Total Antrean Hari Ini</div>
                    <div class="text-2xl font-extrabold text-ink mt-1">
                        {{ $totalQueueToday ?? (count($waitingQueues) + count($completedToday) + ($currentPatient ? 1 : 0)) }}
                    </div>
                    <div class="text-[11px] text-slate-400 mt-0.5">seluruh pasien terdaftar</div>
                </div>
                <div class="w-11 h-11 rounded-hospital bg-primary-50 flex items-center justify-center text-primary shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>

            <!-- 2. Menunggu Dipanggil -->
            <div class="bg-white p-5 rounded-hospital border border-line shadow-xs flex items-center justify-between">
                <div>
                    <div class="text-xs font-medium text-slate-500">Menunggu Dipanggil</div>
                    <div class="text-2xl font-extrabold text-amber-600 mt-1">
                        {{ $waitingCount ?? count($waitingQueues) }}
                    </div>
                    <div class="text-[11px] text-slate-400 mt-0.5">dalam ruang tunggu</div>
                </div>
                <div class="w-11 h-11 rounded-hospital bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <!-- 3. Sedang Diperiksa -->
            <div class="bg-white p-5 rounded-hospital border border-line shadow-xs flex items-center justify-between">
                <div>
                    <div class="text-xs font-medium text-slate-500">Sedang Diperiksa</div>
                    <div class="text-2xl font-extrabold text-primary mt-1">
                        {{ $inProgressCount ?? ($currentPatient ? 1 : 0) }}
                    </div>
                    <div class="text-[11px] text-slate-400 mt-0.5">dalam ruang dokter</div>
                </div>
                <div class="w-11 h-11 rounded-hospital bg-sky-50 flex items-center justify-center text-primary shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
            </div>

            <!-- 4. Selesai Dilayani -->
            <div class="bg-white p-5 rounded-hospital border border-line shadow-xs flex items-center justify-between">
                <div>
                    <div class="text-xs font-medium text-slate-500">Selesai Dilayani</div>
                    <div class="text-2xl font-extrabold text-accent mt-1">
                        {{ $completedCount ?? count($completedToday) }}
                    </div>
                    <div class="text-[11px] text-slate-400 mt-0.5">konsultasi tuntas</div>
                </div>
                <div class="w-11 h-11 rounded-hospital bg-emerald-50 flex items-center justify-center text-accent shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Tab Controls & Quick Action Button -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <!-- Tabs -->
            <div class="bg-white p-1 rounded-hospital border border-line shadow-xs flex flex-wrap gap-1">
                <button @click="activeTab = 'waiting'"
                        :class="activeTab === 'waiting' ? 'bg-primary-50 text-primary border-primary/30 shadow-xs font-bold' : 'text-slate-600 hover:text-ink border-transparent hover:bg-surface font-medium'"
                        class="flex items-center gap-2 px-3.5 py-2 rounded-hospital border text-xs sm:text-sm transition-all">
                    <span>Antrean Menunggu ({{ count($waitingQueues) }})</span>
                </button>

                <button @click="activeTab = 'examination'"
                        :class="activeTab === 'examination' ? 'bg-primary-50 text-primary border-primary/30 shadow-xs font-bold' : 'text-slate-600 hover:text-ink border-transparent hover:bg-surface font-medium'"
                        class="flex items-center gap-2 px-3.5 py-2 rounded-hospital border text-xs sm:text-sm transition-all">
                    <span class="w-2 h-2 rounded-full {{ $currentPatient ? 'bg-accent animate-pulse' : 'bg-slate-300' }}"></span>
                    <span>Dalam Pemeriksaan ({{ $currentPatient ? 1 : 0 }})</span>
                </button>

                <button @click="activeTab = 'completed'"
                        :class="activeTab === 'completed' ? 'bg-primary-50 text-primary border-primary/30 shadow-xs font-bold' : 'text-slate-600 hover:text-ink border-transparent hover:bg-surface font-medium'"
                        class="flex items-center gap-2 px-3.5 py-2 rounded-hospital border text-xs sm:text-sm transition-all">
                    <span>Selesai Hari Ini ({{ count($completedToday) }})</span>
                </button>
            </div>

            <!-- Call Next Action Button -->
            @if($waitingQueues->isNotEmpty())
                <form action="{{ route('doctor.call', $waitingQueues->first()->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="counter" value="Ruang {{ $activeService?->name }}">
                    <button type="submit"
                            class="w-full sm:w-auto px-4 py-2 bg-primary hover:bg-primary-hover text-white text-xs sm:text-sm font-semibold rounded-hospital shadow-xs flex items-center justify-center gap-2 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                        </svg>
                        <span>Panggil Berikutnya</span>
                    </button>
                </form>
            @endif
        </div>

        <!-- TAB 1: ANTREAN MENUNGGU -->
        <div x-show="activeTab === 'waiting'" class="space-y-4">
            <div class="flex items-center justify-between px-1">
                <h3 class="text-sm font-bold text-ink">Antrean Menunggu Dipanggil ({{ count($waitingQueues) }})</h3>
                <span class="text-xs text-slate-500">Diurutkan berdasarkan prioritas klinis triase</span>
            </div>
            <div class="bg-white rounded-hospital border border-line shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="border-b border-line bg-surface text-slate-600 font-semibold text-xs tracking-wider">
                                <th class="py-3 px-4 w-16 text-center">No. Urut</th>
                                <th class="py-3 px-4">No. Antrian</th>
                                <th class="py-3 px-4">Nama Pasien</th>
                                <th class="py-3 px-4">Estimasi</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line font-normal text-ink">
                            @forelse($waitingQueues as $index => $q)
                                <tr class="hover:bg-slate-50/80 transition-colors {{ $q->priority_level === 1 ? 'bg-red-50/40' : '' }}">
                                    <td class="py-3.5 px-4 text-center text-xs text-slate-400 font-medium">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-mono font-bold text-ink text-base">
                                            {{ $q->queue_code }}
                                        </div>
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold uppercase {{ $q->priority_badge_color }} mt-0.5">
                                            {{ $q->priority_label }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-ink text-sm">
                                            {{ $q->patient->name }}
                                        </div>
                                        <div class="text-xs text-slate-500 font-mono mt-0.5">
                                            NIK: {{ $q->patient->nik }} &bull; {{ $q->patient->age ?? '-' }} th &bull; {{ $q->patient->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-slate-600 text-xs">
                                        {{ $q->estimated_call_time ? $q->estimated_call_time->format('H:i') : ($q->created_at ? $q->created_at->format('H:i') : '-') }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            Menunggu
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5" x-data="{ openMenu: false }">
                                            <form action="{{ route('doctor.call', $q->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="counter" value="Ruang {{ $activeService?->name }}">
                                                <button type="submit"
                                                        class="px-3 py-1.5 bg-primary hover:bg-primary-hover text-white rounded-hospital text-xs font-semibold shadow-xs transition-colors flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                                                    <span>Panggil</span>
                                                </button>
                                            </form>

                                            <!-- Dropdown 3-dots -->
                                            <div class="relative">
                                                <button @click="openMenu = !openMenu" @click.outside="openMenu = false"
                                                        class="p-1.5 text-slate-400 hover:text-slate-600 rounded-hospital hover:bg-surface transition-colors">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                                                </button>
                                                <div x-show="openMenu" x-cloak
                                                     class="absolute right-0 mt-1 w-48 bg-white rounded-hospital shadow-lg border border-line py-1.5 z-20 text-left text-xs font-medium text-slate-700">
                                                    <div class="px-3 py-1.5 text-slate-500 text-[11px] font-mono">
                                                        No. Antrean: {{ $q->queue_code }}
                                                    </div>
                                                    @if($q->triageAssessment)
                                                        <div class="px-3 py-1.5 text-slate-700 border-t border-line text-xs">
                                                            <strong>Keluhan:</strong> {{ $q->triageAssessment->chief_complaint }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400">
                                        <div class="max-w-xs mx-auto space-y-2">
                                            <svg class="w-10 h-10 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <p class="font-semibold text-slate-700">Tidak ada antrean menunggu</p>
                                            <p class="text-xs text-slate-500">Semua pasien pada poli {{ $activeService?->name }} telah dipanggil atau dilayani.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Footer Pagination Info -->
                <div class="py-3 px-6 border-t border-line flex items-center justify-between text-xs text-slate-500 bg-surface">
                    <div>
                        Menampilkan {{ count($waitingQueues) }} pasien dalam antrean
                    </div>
                </div>
            </div>

            <!-- Pasien Dilewati / Skipped Section -->
            @if($skippedQueues->isNotEmpty())
                <div class="bg-amber-50/70 rounded-hospital p-4 sm:p-5 border border-amber-200/80">
                    <h3 class="text-xs font-bold text-amber-900 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Pasien Dilewati (Tidak Hadir Saat Dipanggil) — {{ count($skippedQueues) }} Pasien</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($skippedQueues as $sq)
                            <div class="bg-white p-3.5 rounded-hospital border border-amber-200/80 shadow-xs flex items-center justify-between">
                                <div>
                                    <div class="font-mono font-bold text-ink text-sm">{{ $sq->queue_code }}</div>
                                    <div class="text-xs text-slate-600 font-medium">{{ $sq->patient->name }}</div>
                                </div>
                                <form action="{{ route('doctor.call', $sq->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="counter" value="Ruang {{ $activeService?->name }}">
                                    <button type="submit"
                                            class="px-3 py-1 bg-amber-600 hover:bg-amber-700 text-white rounded-hospital text-xs font-semibold transition-colors">
                                        Panggil Ulang
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- TAB 2: DALAM PEMERIKSAAN -->
        <div x-show="activeTab === 'examination'" class="space-y-4" x-cloak>
            @if($currentPatient)
                <div class="bg-white rounded-hospital p-6 sm:p-8 border border-line shadow-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 mb-6 border-b border-line">
                        <div class="flex items-center gap-3">
                            <span class="w-3.5 h-3.5 rounded-full bg-accent animate-pulse"></span>
                            <div>
                                <h2 class="text-lg font-bold text-ink">
                                    Pemeriksaan Pasien Aktif — {{ $activeService?->name }}
                                </h2>
                                <p class="text-xs text-slate-500">Status Pemeriksaan: {{ ucfirst(str_replace('_', ' ', $currentPatient->status)) }}</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-hospital text-xs font-bold uppercase {{ $currentPatient->priority_badge_color }}">
                            {{ $currentPatient->priority_label }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                        <!-- Patient Info Card -->
                        <div class="lg:col-span-5 space-y-4">
                            <div class="p-6 rounded-hospital bg-primary-50 border border-primary/20">
                                <span class="text-xs font-bold text-primary uppercase tracking-wider block">Nomor Antrean Pasien</span>
                                <span class="text-4xl font-extrabold font-mono text-ink mt-1 block">{{ $currentPatient->queue_code }}</span>
                                <h3 class="text-lg font-bold text-ink mt-2">{{ $currentPatient->patient->name }}</h3>
                                <p class="text-xs text-slate-600 font-mono mt-0.5">
                                    NIK: {{ $currentPatient->patient->nik }} &bull; {{ $currentPatient->patient->age ?? '-' }} th ({{ $currentPatient->patient->gender === 'L' ? 'Laki-laki' : 'Perempuan' }})
                                </p>
                            </div>

                            @if($currentPatient->triageAssessment)
                                <div class="p-4 rounded-hospital bg-surface border border-line space-y-2 text-xs">
                                    <div class="font-bold text-slate-800 flex items-center justify-between">
                                        <span>Hasil Triase Klinis</span>
                                        <span class="uppercase font-bold text-[10px] px-2 py-0.5 rounded bg-white border border-line text-slate-800">
                                            Kategori {{ $currentPatient->triageAssessment->category }}
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 font-mono text-[11px] pt-2 border-t border-line">
                                        <div>TD: <strong>{{ $currentPatient->triageAssessment->systolic_bp && $currentPatient->triageAssessment->diastolic_bp ? "{$currentPatient->triageAssessment->systolic_bp}/{$currentPatient->triageAssessment->diastolic_bp} mmHg" : '-' }}</strong></div>
                                        <div>Nadi: <strong>{{ $currentPatient->triageAssessment->heart_rate ? "{$currentPatient->triageAssessment->heart_rate} x/mnt" : '-' }}</strong></div>
                                        <div>Suhu: <strong>{{ $currentPatient->triageAssessment->temperature ? "{$currentPatient->triageAssessment->temperature} °C" : '-' }}</strong></div>
                                        <div>SpO2: <strong>{{ $currentPatient->triageAssessment->oxygen_saturation ? "{$currentPatient->triageAssessment->oxygen_saturation} %" : '-' }}</strong></div>
                                    </div>
                                    <div class="pt-2 text-slate-700">
                                        <span class="font-bold text-slate-800">Keluhan Utama:</span> {{ $currentPatient->triageAssessment->chief_complaint }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Action Form -->
                        <div class="lg:col-span-7">
                            @if($currentPatient->status === 'called')
                                <div class="p-8 text-center bg-surface rounded-hospital border border-line space-y-4">
                                    <div class="w-12 h-12 rounded-hospital bg-primary-50 text-primary flex items-center justify-center mx-auto">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-ink">Pasien Sedang Dipanggil</h4>
                                        <p class="text-xs text-slate-500 mt-1">Silakan mulai konsultasi begitu pasien memasuki ruangan periksa.</p>
                                    </div>
                                    <div class="flex items-center justify-center gap-3 pt-2">
                                        <form action="{{ route('doctor.start', $currentPatient->id) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                    class="px-5 py-2.5 bg-primary hover:bg-primary-hover text-white font-semibold text-xs rounded-hospital shadow-xs transition-colors">
                                                Mulai Pemeriksaan &rarr;
                                            </button>
                                        </form>
                                        <form action="{{ route('doctor.skip', $currentPatient->id) }}" method="POST"
                                              onsubmit="return confirm('Lewati pasien ini jika tidak hadir?')">
                                            @csrf
                                            <input type="hidden" name="reason" value="Tidak hadir setelah dipanggil ke ruangan">
                                            <button type="submit"
                                                    class="px-4 py-2.5 bg-white border border-line hover:bg-surface text-slate-700 font-semibold text-xs rounded-hospital transition-colors">
                                                Lewati Pasien
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @elseif($currentPatient->status === 'in_consultation')
                                <form action="{{ route('doctor.complete', $currentPatient->id) }}" method="POST" class="space-y-4">
                                    @csrf
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan Ringkas Pelayanan & Terapi (Minimisasi Data)</label>
                                        <textarea name="clinical_notes" rows="4" placeholder="Catatan diagnosis umum, edukasi pasien, resep yang diberikan..."
                                                  class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-1 focus:ring-primary">{{ old('clinical_notes') }}</textarea>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Disposisi Pasien Selesai Pemeriksaan *</label>
                                        <select name="disposition" required class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                                            <option value="pulang">Pulang (Rawat Jalan / Selesai Pengobatan)</option>
                                            <option value="kontrol_ulang">Jadwal Kontrol Ulang</option>
                                            <option value="rujuk_lanjut">Rujuk Lanjutan (Rumah Sakit / Spesialis)</option>
                                            <option value="rawat_inap">Rawat Inap Puskesmas</option>
                                        </select>
                                    </div>

                                    <div class="pt-3 flex items-center justify-between">
                                        <span class="text-xs text-slate-500">Durasi dihitung otomatis saat diselesaikan.</span>
                                        <button type="submit"
                                                class="px-5 py-2.5 bg-accent hover:bg-accent-hover text-white font-semibold text-sm rounded-hospital shadow-xs transition-all">
                                            Selesaikan Pelayanan &rarr;
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-hospital p-12 text-center border border-line shadow-xs">
                    <div class="w-12 h-12 rounded-hospital bg-surface text-slate-400 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-800">Ruang Pemeriksaan Siap</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Saat ini belum ada pasien di dalam ruang konsultasi. Silakan panggil pasien berikutnya dari daftar antrean.</p>
                    <button type="button" @click="activeTab = 'waiting'"
                            class="mt-4 px-4 py-2 bg-surface hover:bg-slate-100 border border-line text-slate-700 rounded-hospital text-xs font-semibold transition-colors">
                        Buka Daftar Antrean
                    </button>
                </div>
            @endif
        </div>

        <!-- TAB 3: SELESAI HARI INI -->
        <div x-show="activeTab === 'completed'" class="space-y-4" x-cloak>
            <div class="flex items-center justify-between px-1">
                <h3 class="text-sm font-bold text-ink">Selesai Pelayanan Hari Ini ({{ count($completedToday) }})</h3>
                <span class="text-xs text-slate-500">Daftar riwayat konsultasi medis yang telah dirampungkan hari ini</span>
            </div>
            <div class="bg-white rounded-hospital border border-line shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="border-b border-line bg-surface text-slate-600 font-semibold text-xs tracking-wider">
                                <th class="py-3 px-4">No. Antrean</th>
                                <th class="py-3 px-4">Nama Pasien</th>
                                <th class="py-3 px-4">Selesai Jam</th>
                                <th class="py-3 px-4">Durasi Pelayanan</th>
                                <th class="py-3 px-4">Disposisi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line font-normal text-ink">
                            @forelse($completedToday as $cq)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3.5 px-4 font-mono font-bold text-ink">
                                        {{ $cq->queue_code }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-ink">{{ $cq->patient->name }}</div>
                                        <div class="text-xs text-slate-500 font-mono">NIK: {{ $cq->patient->nik }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-xs text-slate-700">
                                        {{ $cq->served_at ? $cq->served_at->format('H:i') . ' WIB' : '-' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-xs text-slate-600">
                                        {{ $cq->service_duration_minutes ? "{$cq->service_duration_minutes} menit" : '-' }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-accent border border-emerald-200">
                                            Selesai
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-400">
                                        Belum ada pasien yang selesai dilayani hari ini.
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
