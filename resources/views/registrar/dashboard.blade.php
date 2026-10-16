<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-ink tracking-tight">
                    {{ __('Loket Pendaftaran & Registrasi Pasien') }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 font-normal mt-0.5">
                    Kelola reservasi online, pendaftaran walk-in, dan proses check-in pasien faskes.
                </p>
            </div>

            <!-- Date & Time Widget + Monitor Shortcut -->
            <div class="flex items-center gap-3">
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

                <a href="{{ route('public.queue') }}" target="_blank"
                   class="inline-flex items-center gap-2 px-3 py-2 bg-white hover:bg-surface border border-line text-slate-700 text-xs font-semibold rounded-hospital shadow-xs transition-colors"
                   title="Buka Layar Monitor Antrean Ruang Tunggu">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>Layar Monitor</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{ activeTab: '{{ request('tab', 'online') }}' }">
        <!-- Tab Navigation Buttons -->
        <div class="bg-white p-1 rounded-hospital border border-line shadow-xs flex flex-wrap gap-1 w-full sm:w-auto">
            <button @click="activeTab = 'online'"
                    :class="activeTab === 'online' ? 'bg-primary-50 text-primary border-primary/30 shadow-xs font-bold' : 'text-slate-600 hover:text-ink border-transparent hover:bg-surface font-medium'"
                    class="flex items-center gap-2 px-3.5 py-2 rounded-hospital border text-xs sm:text-sm transition-all">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Reservasi Online</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold"
                      :class="activeTab === 'online' ? 'bg-primary/10 text-primary' : 'bg-surface text-slate-500'">
                    {{ $onlineCount ?? count($appointmentsToday) }}
                </span>
            </button>

            <button @click="activeTab = 'walkin'"
                    :class="activeTab === 'walkin' ? 'bg-primary-50 text-primary border-primary/30 shadow-xs font-bold' : 'text-slate-600 hover:text-ink border-transparent hover:bg-surface font-medium'"
                    class="flex items-center gap-2 px-3.5 py-2 rounded-hospital border text-xs sm:text-sm transition-all">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                <span>Pendaftaran Walk-in</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold"
                      :class="activeTab === 'walkin' ? 'bg-primary/10 text-primary' : 'bg-surface text-slate-500'">
                    {{ $walkInCount ?? 0 }}
                </span>
            </button>

            <button @click="activeTab = 'queues'"
                    :class="activeTab === 'queues' ? 'bg-primary-50 text-primary border-primary/30 shadow-xs font-bold' : 'text-slate-600 hover:text-ink border-transparent hover:bg-surface font-medium'"
                    class="flex items-center gap-2 px-3.5 py-2 rounded-hospital border text-xs sm:text-sm transition-all">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                <span>Riwayat Hari Ini</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold"
                      :class="activeTab === 'queues' ? 'bg-primary/10 text-primary' : 'bg-surface text-slate-500'">
                    {{ count($todayQueues) }}
                </span>
            </button>
        </div>

        <!-- TAB 1: RESERVASI ONLINE -->
        <div x-show="activeTab === 'online'" class="space-y-4">
            <!-- Filter & Action Bar -->
            <div class="bg-white p-3.5 sm:p-4 rounded-hospital border border-line shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
                <form action="{{ route('registrar.dashboard') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 flex-1">
                    <!-- Search Input -->
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ $search }}"
                               placeholder="Cari No. Reservasi, NIK, atau Nama Pasien..."
                               class="w-full pl-10 pr-4 py-2 text-xs sm:text-sm rounded-hospital border border-line bg-surface focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary placeholder-slate-400 transition-colors">
                    </div>

                    <!-- Status Filter Dropdown -->
                    <div class="sm:w-44">
                        <select name="status" onchange="this.form.submit()"
                                class="w-full py-2 pl-3.5 pr-8 text-xs sm:text-sm rounded-hospital border border-line bg-surface focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary text-slate-700 font-medium transition-colors">
                            <option value="">Semua Status</option>
                            <option value="booked" {{ request('status') === 'booked' ? 'selected' : '' }}>Menunggu Check-In</option>
                            <option value="checked_in" {{ request('status') === 'checked_in' ? 'selected' : '' }}>Checked In</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>

                    @if($search || request('status'))
                        <a href="{{ route('registrar.dashboard') }}"
                           class="px-3 py-2 bg-surface hover:bg-slate-100 border border-line text-slate-600 rounded-hospital text-xs font-semibold text-center transition-colors">
                            Reset
                        </a>
                    @endif
                </form>

                <!-- Primary Action: + Pendaftaran Walk-in -->
                <button @click="activeTab = 'walkin'"
                        class="px-3.5 py-2 bg-primary hover:bg-primary-hover text-white text-xs sm:text-sm font-semibold rounded-hospital shadow-xs flex items-center justify-center gap-2 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>+ Pendaftaran Walk-in</span>
                </button>
            </div>

            <!-- Reservasi Table -->
            <div class="bg-white rounded-hospital border border-line shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="border-b border-line bg-surface text-slate-600 font-semibold text-xs tracking-wider">
                                <th class="py-3 px-4 w-12 text-center">No.</th>
                                <th class="py-3 px-4">Waktu</th>
                                <th class="py-3 px-4">No. Reservasi</th>
                                <th class="py-3 px-4">Nama Pasien</th>
                                <th class="py-3 px-4">Poli Tujuan</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line font-normal text-ink">
                            @forelse($appointmentsToday as $index => $appt)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3.5 px-4 text-center text-xs text-slate-400 font-medium">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="py-3.5 px-4 font-medium text-ink whitespace-nowrap">
                                        {{ substr($appt->slot_time, 0, 5) }} WIB
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-mono font-semibold text-ink text-xs">
                                            {{ $appt->reservation_code ?? $appt->appointment_number }}
                                        </div>
                                        <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-semibold bg-primary-50 text-primary border border-primary/20 mt-0.5">
                                            Online
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-ink text-sm">
                                            {{ $appt->patient->name }}
                                        </div>
                                        <div class="text-xs text-slate-500 font-mono mt-0.5">
                                            NIK: {{ $appt->patient->nik }}
                                        </div>
                                        <div class="mt-1">
                                            @if($appt->patient->bpjs_number)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200" title="Subsidi Penuh — Tanpa Tagihan & Tanpa Iuran">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Bebas Biaya (Rp 0)
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-sky-50 text-sky-800 border border-sky-200" title="Mandiri/Reguler — Estimasi Rp 25.000 On-the-spot/Digital">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                                    Mandiri • On-the-spot/Digital
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 font-medium text-slate-800">
                                        {{ $appt->service->name }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($appt->status === 'checked_in')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-accent border border-emerald-200">
                                                Checked In
                                            </span>
                                        @elseif($appt->status === 'cancelled' || $appt->status === 'no_show')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-600 border border-red-200">
                                                Cancelled
                                            </span>
                                        @elseif($appt->status === 'completed')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-sky-50 text-primary border border-sky-200">
                                                Selesai
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                Menunggu
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5" x-data="{ openMenu: false }">
                                            @if($appt->status === 'booked')
                                                <form action="{{ route('registrar.check_in', $appt->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                            class="px-3 py-1.5 bg-accent hover:bg-accent-hover text-white rounded-hospital text-xs font-semibold shadow-xs transition-colors flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                        <span>Check-In</span>
                                                    </button>
                                                </form>
                                            @elseif($appt->queueEntry)
                                                <a href="{{ route('registrar.ticket', $appt->queueEntry->id) }}"
                                                   class="px-3 py-1.5 border border-line text-slate-700 hover:bg-surface rounded-hospital text-xs font-medium transition-colors">
                                                    Lihat Tiket
                                                </a>
                                            @else
                                                <span class="px-3 py-1.5 border border-line text-slate-400 rounded-hospital text-xs font-medium">
                                                    Lihat
                                                </span>
                                            @endif

                                            <!-- Dropdown 3-dots -->
                                            <div class="relative">
                                                <button @click="openMenu = !openMenu" @click.outside="openMenu = false"
                                                        class="p-1.5 text-slate-400 hover:text-slate-600 rounded-hospital hover:bg-surface transition-colors">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                                    </svg>
                                                </button>
                                                <div x-show="openMenu" x-cloak
                                                     class="absolute right-0 mt-1 w-44 bg-white rounded-hospital shadow-lg border border-line py-1.5 z-20 text-left text-xs font-medium text-slate-700">
                                                    @if($appt->queueEntry)
                                                        <a href="{{ route('registrar.ticket', $appt->queueEntry->id) }}" class="flex items-center gap-2 px-3.5 py-2 hover:bg-surface text-slate-700">
                                                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                            <span>Cetak Tiket Antrean</span>
                                                        </a>
                                                    @endif
                                                    <div class="px-3.5 py-1 text-[11px] text-slate-400 border-t border-line">
                                                        ID Sistem: #{{ $appt->id }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-400">
                                        <div class="max-w-xs mx-auto space-y-2">
                                            <svg class="w-10 h-10 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                            <p class="font-semibold text-slate-700">Tidak ada reservasi ditemukan</p>
                                            <p class="text-xs text-slate-500">Gunakan kata kunci pencarian lain atau gunakan formulir pendaftaran walk-in.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer -->
                <div class="py-3 px-6 border-t border-line flex items-center justify-between text-xs text-slate-500 bg-surface">
                    <div>
                        Menampilkan {{ count($appointmentsToday) }} reservasi hari ini
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: PENDAFTARAN WALK-IN -->
        <div x-show="activeTab === 'walkin'" class="max-w-3xl mx-auto" x-cloak>
            <form action="{{ route('registrar.walk_in') }}" method="POST"
                  class="bg-white rounded-hospital p-6 sm:p-8 border border-line shadow-xs space-y-6">
                @csrf

                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-ink">Formulir Pendaftaran Pasien Walk-in</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Pasien datang langsung ke loket faskes. Nomor antrean poli akan langsung diterbitkan.</p>
                </div>

                <!-- Emergency Alert Banner -->
                <div class="p-4 rounded-hospital bg-red-50 border border-red-200 flex items-start gap-3">
                    <input type="checkbox" id="is_emergency" name="is_emergency" value="1"
                           class="rounded-hospital border-red-300 text-red-600 focus:ring-red-500 mt-0.5 w-4 h-4">
                    <label for="is_emergency" class="text-xs cursor-pointer">
                        <span class="font-bold text-red-900 block text-sm">Jalur Respons Darurat (Prioritas 1 Merah)</span>
                        <span class="text-red-700">Centang jika kondisi pasien darurat/kritis (sesak napas akut, penurunan kesadaran, trauma hebat). Pasien akan langsung diprioritaskan.</span>
                    </label>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Poli Tujuan *</label>
                        <select name="service_id" required
                                class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-primary">
                            <option value="">-- Pilih Poli Tujuan --</option>
                            @foreach($services as $svc)
                                <option value="{{ $svc->id }}">{{ $svc->name }} ({{ $svc->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">NIK Pasien (16 Digit) *</label>
                        <input type="text" name="nik" maxlength="16" required placeholder="Contoh: 3201012345670001"
                               class="w-full rounded-hospital border-line text-sm font-mono focus:border-primary focus:ring-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Pasien *</label>
                        <input type="text" name="name" required placeholder="Nama lengkap sesuai KTP"
                               class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor WhatsApp / HP *</label>
                        <input type="text" name="phone" required placeholder="08xxxxxxxxxx"
                               class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tanggal Lahir *</label>
                        <input type="date" name="date_of_birth" required
                               class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Jenis Kelamin *</label>
                        <select name="gender" required
                                class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-primary">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">No. BPJS (Opsional)</label>
                        <input type="text" name="bpjs_number" placeholder="Nomor kartu BPJS jika ada"
                               x-on:input="if ($event.target.value.trim().length > 0) { golongan = 'subsidi'; }"
                               class="w-full rounded-hospital border-line text-sm font-mono focus:border-primary focus:ring-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Alamat Singkat</label>
                        <input type="text" name="address" placeholder="Desa / Kelurahan"
                               class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-primary">
                    </div>

                    <!-- Kategori Golongan Pasien & Transparansi Biaya -->
                    <div class="sm:col-span-2 pt-2 border-t border-line" x-data="{ golongan: 'subsidi', paymentMethod: 'loket' }">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">
                            Transparansi Subsidi &amp; Pembayaran Berdasarkan Golongan *
                        </label>
                        <p class="text-[11px] text-slate-500 mb-3">
                            Pilih golongan pembiayaan pasien untuk kepastian tarif dan transparansi administrasi loket.
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- Pilihan 1: Subsidi Penuh (Menengah ke Bawah) -->
                            <label class="relative block cursor-pointer">
                                <input type="radio" name="patient_tier" value="subsidi" x-model="golongan" class="sr-only peer">
                                <div class="p-3.5 rounded-hospital border transition-all h-full flex flex-col justify-between"
                                     :class="golongan === 'subsidi' ? 'border-emerald-500 bg-emerald-50/40 ring-1 ring-emerald-500 shadow-xs' : 'border-line hover:border-slate-300 bg-white'">
                                    <div>
                                        <div class="flex items-center justify-between mb-1.5">
                                            <span class="text-xs font-bold text-ink">Subsidi Penuh</span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                Bebas Biaya (Rp 0)
                                            </span>
                                        </div>
                                        <p class="text-[11px] text-slate-600 font-medium">Golongan Menengah ke Bawah / KIS / BPJS PBI</p>
                                        <div class="mt-2.5 p-2 rounded bg-white border border-emerald-200 text-[11px] text-emerald-900 space-y-1">
                                            <div class="flex items-center gap-1.5 font-bold">
                                                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                <span>Status: Bebas Biaya (Rp 0)</span>
                                            </div>
                                            <p class="text-[10px] text-slate-600 leading-snug">
                                                Ditampilkan status <strong>"Bebas Biaya (Rp 0)"</strong> secara transparan tanpa iuran bulanan dan tanpa tagihan sama sekali.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-2 text-[10px] text-emerald-700 font-semibold flex items-center gap-1">
                                        <span>• Tanpa iuran bulanan</span>
                                        <span>• Tanpa tagihan sama sekali</span>
                                    </div>
                                </div>
                            </label>

                            <!-- Pilihan 2: Mandiri/Reguler (Menengah ke Atas) -->
                            <label class="relative block cursor-pointer">
                                <input type="radio" name="patient_tier" value="mandiri" x-model="golongan" class="sr-only peer">
                                <div class="p-3.5 rounded-hospital border transition-all h-full flex flex-col justify-between"
                                     :class="golongan === 'mandiri' ? 'border-sky-500 bg-sky-50/40 ring-1 ring-sky-500 shadow-xs' : 'border-line hover:border-slate-300 bg-white'">
                                    <div>
                                        <div class="flex items-center justify-between mb-1.5">
                                            <span class="text-xs font-bold text-ink">Mandiri / Reguler</span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-100 text-sky-800 border border-sky-300">
                                                Pasien Umum
                                            </span>
                                        </div>
                                        <p class="text-[11px] text-slate-600 font-medium">Golongan Menengah ke Atas / Non-Subsidi</p>
                                        <div class="mt-2.5 p-2 rounded bg-white border border-sky-200 text-[11px] space-y-1">
                                            <div class="flex justify-between text-slate-600 text-[10px]">
                                                <span>Retribusi Pendaftaran:</span>
                                                <span class="font-mono font-semibold text-ink">Rp 10.000</span>
                                            </div>
                                            <div class="flex justify-between text-slate-600 text-[10px]">
                                                <span>Jasa Konsultasi &amp; Skrining:</span>
                                                <span class="font-mono font-semibold text-ink">Rp 15.000</span>
                                            </div>
                                            <div class="flex justify-between pt-1 border-t border-slate-100 font-bold text-sky-900 text-[10px]">
                                                <span>Total Estimasi Dasar:</span>
                                                <span class="font-mono">Rp 25.000</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Opsi Pembayaran Mandiri -->
                                    <div class="mt-2 pt-2 border-t border-sky-100 text-[10px]">
                                        <span class="font-bold text-slate-700 block mb-1">Opsi Metode Pembayaran:</span>
                                        <div class="flex items-center gap-2">
                                            <label class="inline-flex items-center gap-1 cursor-pointer">
                                                <input type="radio" name="payment_method" value="loket" x-model="paymentMethod" class="w-3 h-3 text-sky-600 focus:ring-sky-500">
                                                <span class="text-slate-700">On-the-spot di Loket</span>
                                            </label>
                                            <label class="inline-flex items-center gap-1 cursor-pointer">
                                                <input type="radio" name="payment_method" value="digital" x-model="paymentMethod" class="w-3 h-3 text-sky-600 focus:ring-sky-500">
                                                <span class="text-slate-700">Digital (QRIS)</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-line flex items-center justify-end gap-3">
                    <button type="button" @click="activeTab = 'online'"
                            class="px-4 py-2.5 text-xs font-semibold text-slate-600 hover:bg-surface border border-line rounded-hospital transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 bg-primary hover:bg-primary-hover text-white font-semibold text-xs sm:text-sm rounded-hospital shadow-xs transition-all">
                        Daftarkan & Terbitkan Antrean &rarr;
                    </button>
                </div>
            </form>
        </div>

        <!-- TAB 3: ANTREAN HARI INI -->
        <div x-show="activeTab === 'queues'" class="space-y-4" x-cloak>
            <div class="bg-white rounded-hospital border border-line shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="border-b border-line bg-surface text-slate-600 font-semibold text-xs tracking-wider">
                                <th class="py-3 px-4">No. Antrean</th>
                                <th class="py-3 px-4">Nama Pasien</th>
                                <th class="py-3 px-4">Poli & Jenis</th>
                                <th class="py-3 px-4">Prioritas Triase</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line font-normal text-ink">
                            @forelse($todayQueues as $q)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3.5 px-4">
                                        <div class="font-mono font-bold text-ink text-base">{{ $q->queue_code }}</div>
                                        <div class="text-[11px] text-slate-500 font-mono">{{ $q->created_at->format('H:i') }} WIB</div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-ink text-sm">{{ $q->patient->name }}</div>
                                        <div class="text-xs text-slate-500 font-mono">NIK: {{ $q->patient->nik }}</div>
                                        <div class="mt-1">
                                            @if($q->patient->bpjs_number)
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                    Bebas Biaya (Rp 0)
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded text-[10px] font-medium bg-sky-50 text-sky-800 border border-sky-200">
                                                    Mandiri (On-the-spot/Digital)
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-slate-800">{{ $q->service->name }}</div>
                                        <div class="text-[10px] text-slate-500 uppercase font-semibold">{{ $q->registration_type }}</div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-block px-2.5 py-1 rounded-hospital text-[11px] font-semibold uppercase {{ $q->priority_badge_color }}">
                                            {{ $q->priority_label }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="font-semibold text-xs text-slate-700">{{ $q->status_label }}</span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap space-x-2">
                                        <a href="{{ route('registrar.ticket', $q->id) }}"
                                           class="inline-block px-3 py-1.5 bg-surface hover:bg-slate-100 border border-line rounded-hospital text-slate-700 font-semibold text-xs transition-colors">
                                            Cetak Tiket
                                        </a>
                                        @if(!in_array($q->status, ['completed', 'cancelled']))
                                            <form action="{{ route('registrar.cancel', $q->id) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Yakin ingin membatalkan antrean ini?')">
                                                @csrf
                                                <input type="hidden" name="reason" value="Dibatalkan oleh pasien di loket">
                                                <button type="submit"
                                                        class="px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-hospital font-semibold text-xs transition-colors">
                                                    Batal
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400">
                                        Belum ada antrean yang terdaftar untuk hari ini.
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
