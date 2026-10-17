<x-app-layout>
    <x-slot name="header">
        <h1 class="font-extrabold text-xl sm:text-2xl text-ink tracking-tight">
            {{ __('Pengaturan Sistem & Administrasi Puskesmas') }}
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 font-normal mt-0.5">
            Kelola konfigurasi slot waktu faskes, poliklinik aktif, dan hak akses pengguna.
        </p>
    </x-slot>

    <div class="space-y-6" x-data="{ tab: '{{ session('active_tab', 'settings') }}' }">
        <!-- Tabs -->
        <div class="bg-white p-1 rounded-hospital border border-line shadow-xs flex flex-wrap gap-1">
            <button @click="tab = 'settings'"
                    :class="tab === 'settings' ? 'bg-primary-50 text-primary border-primary/30 shadow-xs font-bold' : 'text-slate-600 hover:text-ink border-transparent hover:bg-surface font-medium'"
                    class="py-2 px-3.5 border rounded-hospital text-xs sm:text-sm transition-all">
                1. Pengaturan Operasional & Slot
            </button>
            <button @click="tab = 'services'"
                    :class="tab === 'services' ? 'bg-primary-50 text-primary border-primary/30 shadow-xs font-bold' : 'text-slate-600 hover:text-ink border-transparent hover:bg-surface font-medium'"
                    class="py-2 px-3.5 border rounded-hospital text-xs sm:text-sm transition-all">
                2. Manajemen Layanan / Poli
            </button>
            <button @click="tab = 'schedules'"
                    :class="tab === 'schedules' ? 'bg-primary-50 text-primary border-primary/30 shadow-xs font-bold' : 'text-slate-600 hover:text-ink border-transparent hover:bg-surface font-medium'"
                    class="py-2 px-3.5 border rounded-hospital text-xs sm:text-sm transition-all">
                3. Jadwal Praktik Dokter
            </button>
            <button @click="tab = 'users'"
                    :class="tab === 'users' ? 'bg-primary-50 text-primary border-primary/30 shadow-xs font-bold' : 'text-slate-600 hover:text-ink border-transparent hover:bg-surface font-medium'"
                    class="py-2 px-3.5 border rounded-hospital text-xs sm:text-sm transition-all">
                4. Manajemen Akun & Hak Akses
            </button>
        </div>

        <!-- TAB 1: PENGATURAN OPERASIONAL -->
        <div x-show="tab === 'settings'" class="max-w-3xl">
            <form action="{{ route('admin.settings.update') }}" method="POST"
                  class="bg-white rounded-hospital p-6 sm:p-8 border border-line shadow-xs space-y-6">
                @csrf

                <div>
                    <h3 class="text-base sm:text-lg font-bold text-ink">Konfigurasi Jam & Durasi Pelayanan</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Konfigurasi ini mengontrol pembuatan slot reservasi online dan estimasi waktu tunggu pasien.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Jam Buka Pelayanan *</label>
                        <input type="time" name="opening_time" value="{{ old('opening_time', $settings['opening_time']) }}" required
                               class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Jam Tutup Pelayanan *</label>
                        <input type="time" name="closing_time" value="{{ old('closing_time', $settings['closing_time']) }}" required
                               class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Durasi Slot Standar (Menit) *</label>
                        <input type="number" name="default_slot_duration_minutes" value="{{ old('default_slot_duration_minutes', $settings['default_slot_duration_minutes']) }}" min="5" max="120" required
                               class="w-full rounded-hospital border-line text-sm font-mono focus:border-primary focus:ring-primary">
                        <p class="text-[11px] text-slate-500 mt-1">Standar 20 menit (dapat disesuaikan sesuai beban kerja poli).</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kapasitas Maksimal per Slot *</label>
                        <input type="number" name="max_slot_capacity" value="{{ old('max_slot_capacity', $settings['max_slot_capacity']) }}" min="1" max="50" required
                               class="w-full rounded-hospital border-line text-sm font-mono focus:border-primary focus:ring-primary">
                        <p class="text-[11px] text-slate-500 mt-1">Jumlah pasien reservasi maksimal dalam satu rentang slot waktu.</p>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Toleransi Keterlambatan Check-in (Menit) *</label>
                        <input type="number" name="late_tolerance_minutes" value="{{ old('late_tolerance_minutes', $settings['late_tolerance_minutes']) }}" min="0" max="120" required
                               class="w-full rounded-hospital border-line text-sm font-mono focus:border-primary focus:ring-primary">
                        <p class="text-[11px] text-slate-500 mt-1">Batas waktu keterlambatan pasien dari jam slot sebelum status diubah otomatis menjadi No Show.</p>
                    </div>
                </div>

                <div class="pt-4 border-t border-line flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-primary hover:bg-primary-hover text-white font-semibold text-xs sm:text-sm rounded-hospital shadow-xs transition-colors">
                        Simpan Perubahan Pengaturan
                    </button>
                </div>
            </form>
        </div>

        <!-- TAB 2: MANAJEMEN POLI -->
        <div x-show="tab === 'services'" class="space-y-6" x-cloak>
            <!-- Tambah Poli Baru -->
            <div class="bg-white rounded-hospital p-6 sm:p-8 border border-line shadow-xs max-w-3xl">
                <h3 class="text-base font-bold text-ink mb-4">Tambah Poli Layanan Baru</h3>
                <form action="{{ route('admin.services.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Poli *</label>
                        <input type="text" name="name" required placeholder="Contoh: Poli Mata" class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kode Poli (Maks 5 Huruf) *</label>
                        <input type="text" name="code" required maxlength="5" placeholder="Contoh: MAT" class="w-full rounded-hospital border-line text-sm font-mono uppercase focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Durasi Slot (Menit) *</label>
                        <input type="number" name="slot_duration_minutes" value="20" required class="w-full rounded-hospital border-line text-sm font-mono focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kapasitas Slot *</label>
                        <input type="number" name="max_capacity_per_slot" value="4" required class="w-full rounded-hospital border-line text-sm font-mono focus:border-primary focus:ring-primary">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Deskripsi Layanan</label>
                        <input type="text" name="description" placeholder="Penjelasan singkat ruang lingkup layanan..." class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-primary">
                    </div>
                    <div class="sm:col-span-2 flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-hover text-white font-semibold text-xs rounded-hospital shadow-xs transition-colors">
                            + Tambah Poli
                        </button>
                    </div>
                </form>
            </div>

            <!-- Daftar Poli -->
            <div class="bg-white rounded-hospital border border-line shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-surface border-b border-line text-slate-600 font-semibold uppercase tracking-wider">
                            <tr>
                                <th class="p-3.5">Kode & Nama Poli</th>
                                <th class="p-3.5">Durasi Slot</th>
                                <th class="p-3.5">Kapasitas Slot</th>
                                <th class="p-3.5">Status</th>
                                <th class="p-3.5 text-center">Aksi Update</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line font-medium text-slate-700">
                            @foreach($services as $svc)
                                <tr>
                                    <form action="{{ route('admin.services.update', $svc->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <td class="p-3.5">
                                            <input type="text" name="name" value="{{ $svc->name }}" required class="rounded-hospital border-line text-xs font-bold">
                                            <input type="text" name="code" value="{{ $svc->code }}" maxlength="5" required class="rounded-hospital border-line text-xs font-mono uppercase w-20 ms-1">
                                        </td>
                                        <td class="p-3.5">
                                            <input type="number" name="slot_duration_minutes" value="{{ $svc->slot_duration_minutes }}" required class="rounded-hospital border-line text-xs font-mono w-20"> mnt
                                        </td>
                                        <td class="p-3.5">
                                            <input type="number" name="max_capacity_per_slot" value="{{ $svc->max_capacity_per_slot }}" required class="rounded-hospital border-line text-xs font-mono w-20"> pasien
                                        </td>
                                        <td class="p-3.5">
                                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                                <input type="checkbox" name="is_active" value="1" {{ $svc->is_active ? 'checked' : '' }} class="rounded-hospital border-line text-primary focus:ring-primary">
                                                <span class="text-xs">{{ $svc->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                            </label>
                                        </td>
                                        <td class="p-3.5 text-center">
                                            <button type="submit" class="px-3 py-1.5 bg-surface hover:bg-slate-100 border border-line text-slate-800 font-semibold text-[11px] rounded-hospital transition-colors">
                                                Simpan
                                            </button>
                                        </td>
                                    </form>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 3: JADWAL PRAKTIK DOKTER -->
        <div x-show="tab === 'schedules'" class="space-y-6" x-cloak>
            <!-- Tambah Jadwal Dokter Baru -->
            <div class="bg-white rounded-hospital p-6 sm:p-8 border border-line shadow-xs max-w-3xl">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-base font-bold text-ink">Tambah Jadwal Praktik Dokter</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Tentukan dokter yang bertugas, poli pelayanan, hari, dan rentang jam praktik.</p>
                    </div>
                    <span class="text-[11px] font-bold text-primary bg-primary-50 px-2.5 py-1 rounded-full border border-primary/20">
                        {{ $doctors->count() }} Dokter Terdaftar
                    </span>
                </div>

                @if($doctors->isEmpty())
                    <div class="p-4 rounded-hospital bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Belum ada akun dokter terdaftar. Silakan buat akun dengan peran <strong>Dokter</strong> di tab Manajemen Akun terlebih dahulu.</span>
                    </div>
                @else
                    <form action="{{ route('admin.schedules.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pilih Dokter *</label>
                            <select name="user_id" required class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-primary">
                                <option value="">-- Pilih Dokter --</option>
                                @foreach($doctors as $doc)
                                    <option value="{{ $doc->id }}" {{ old('user_id') == $doc->id ? 'selected' : '' }}>
                                        {{ $doc->name }} ({{ $doc->phone ?? $doc->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Poli / Layanan *</label>
                            <select name="service_id" required class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-primary">
                                <option value="">-- Pilih Poli --</option>
                                @foreach($services as $svc)
                                    <option value="{{ $svc->id }}" {{ old('service_id') == $svc->id ? 'selected' : '' }}>
                                        {{ $svc->name }} ({{ $svc->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Hari Praktik *</label>
                            <select name="day_of_week" required class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-primary">
                                <option value="1" {{ old('day_of_week') == 1 ? 'selected' : '' }}>Senin</option>
                                <option value="2" {{ old('day_of_week') == 2 ? 'selected' : '' }}>Selasa</option>
                                <option value="3" {{ old('day_of_week') == 3 ? 'selected' : '' }}>Rabu</option>
                                <option value="4" {{ old('day_of_week') == 4 ? 'selected' : '' }}>Kamis</option>
                                <option value="5" {{ old('day_of_week') == 5 ? 'selected' : '' }}>Jumat</option>
                                <option value="6" {{ old('day_of_week') == 6 ? 'selected' : '' }}>Sabtu</option>
                                <option value="7" {{ old('day_of_week') == 7 ? 'selected' : '' }}>Minggu</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Jam Mulai *</label>
                                <input type="time" name="start_time" value="{{ old('start_time', '08:00') }}" required class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-primary">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Jam Selesai *</label>
                                <input type="time" name="end_time" value="{{ old('end_time', '14:00') }}" required class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-primary">
                            </div>
                        </div>

                        <div class="sm:col-span-2 flex items-center justify-between pt-2">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" checked class="rounded-hospital border-line text-primary focus:ring-primary">
                                <span class="text-xs font-semibold text-slate-700">Jadwal Langsung Aktif</span>
                            </label>

                            <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-hover text-white font-semibold text-xs rounded-hospital shadow-xs transition-colors">
                                + Simpan Jadwal Praktik
                            </button>
                        </div>
                    </form>
                @endif
            </div>

            <!-- Daftar Jadwal Dokter -->
            <div class="bg-white rounded-hospital border border-line shadow-xs overflow-hidden">
                <div class="p-4 bg-surface border-b border-line flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h4 class="font-bold text-sm text-ink">Daftar Jadwal Praktik Dokter Aktif</h4>
                        <p class="text-xs text-slate-500">Jadwal di bawah ini yang akan tampil kepada pasien saat membuat tiket antrean online.</p>
                    </div>
                    <span class="text-xs font-medium text-slate-600 bg-white px-2.5 py-1 rounded-hospital border border-line">
                        Total: <strong>{{ $schedules->count() }} Jadwal</strong>
                    </span>
                </div>

                @if($schedules->isEmpty())
                    <div class="p-8 text-center text-slate-500 text-xs">
                        Belum ada jadwal dokter yang dikonfigurasi. Tambahkan jadwal menggunakan form di atas.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-surface/60 border-b border-line text-slate-600 font-semibold uppercase tracking-wider">
                                <tr>
                                    <th class="p-3.5">Dokter</th>
                                    <th class="p-3.5">Poli / Layanan</th>
                                    <th class="p-3.5">Hari</th>
                                    <th class="p-3.5">Jam Praktik</th>
                                    <th class="p-3.5">Status</th>
                                    <th class="p-3.5 text-center">Aksi Update &amp; Hapus</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-line font-medium text-slate-700">
                                @foreach($schedules as $sch)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <form action="{{ route('admin.schedules.update', $sch->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <td class="p-3.5">
                                                <div class="flex items-center gap-2.5">
                                                    <div class="w-7 h-7 rounded-full bg-primary-50 text-primary flex items-center justify-center font-bold text-xs shrink-0 border border-primary/20">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                    </div>
                                                    <div>
                                                        <select name="user_id" required class="rounded-hospital border-line text-xs font-bold py-1 px-2">
                                                            @foreach($doctors as $doc)
                                                                <option value="{{ $doc->id }}" {{ $sch->user_id == $doc->id ? 'selected' : '' }}>
                                                                    {{ $doc->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="p-3.5">
                                                <select name="service_id" required class="rounded-hospital border-line text-xs py-1 px-2">
                                                    @foreach($services as $svc)
                                                        <option value="{{ $svc->id }}" {{ $sch->service_id == $svc->id ? 'selected' : '' }}>
                                                            {{ $svc->name }} ({{ $svc->code }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>

                                            <td class="p-3.5">
                                                <select name="day_of_week" required class="rounded-hospital border-line text-xs py-1 px-2 font-medium">
                                                    <option value="1" {{ $sch->day_of_week == 1 ? 'selected' : '' }}>Senin</option>
                                                    <option value="2" {{ $sch->day_of_week == 2 ? 'selected' : '' }}>Selasa</option>
                                                    <option value="3" {{ $sch->day_of_week == 3 ? 'selected' : '' }}>Rabu</option>
                                                    <option value="4" {{ $sch->day_of_week == 4 ? 'selected' : '' }}>Kamis</option>
                                                    <option value="5" {{ $sch->day_of_week == 5 ? 'selected' : '' }}>Jumat</option>
                                                    <option value="6" {{ $sch->day_of_week == 6 ? 'selected' : '' }}>Sabtu</option>
                                                    <option value="7" {{ $sch->day_of_week == 7 ? 'selected' : '' }}>Minggu</option>
                                                </select>
                                            </td>

                                            <td class="p-3.5">
                                                <div class="flex items-center gap-1 font-mono">
                                                    <input type="time" name="start_time" value="{{ substr($sch->start_time, 0, 5) }}" required class="rounded-hospital border-line text-xs py-1 px-1.5 w-20">
                                                    <span>-</span>
                                                    <input type="time" name="end_time" value="{{ substr($sch->end_time, 0, 5) }}" required class="rounded-hospital border-line text-xs py-1 px-1.5 w-20">
                                                </div>
                                            </td>

                                            <td class="p-3.5">
                                                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                                    <input type="checkbox" name="is_active" value="1" {{ $sch->is_active ? 'checked' : '' }} class="rounded-hospital border-line text-primary focus:ring-primary">
                                                    <span class="text-[11px] font-semibold {{ $sch->is_active ? 'text-emerald-700' : 'text-slate-400' }}">
                                                        {{ $sch->is_active ? 'Aktif' : 'Nonaktif' }}
                                                    </span>
                                                </label>
                                            </td>

                                            <td class="p-3.5 text-center">
                                                <div class="inline-flex items-center gap-1.5">
                                                    <button type="submit" class="px-2.5 py-1 bg-surface hover:bg-slate-100 border border-line text-slate-800 font-semibold text-[11px] rounded-hospital transition-colors">
                                                        Simpan
                                                    </button>
                                        </form>

                                                    <form action="{{ route('admin.schedules.destroy', $sch->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal dokter ini?');" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="px-2 py-1 bg-red-50 hover:bg-red-100 border border-red-200 text-red-700 font-semibold text-[11px] rounded-hospital transition-colors">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- TAB 4: MANAJEMEN AKUN & HAK AKSES -->
        <div x-show="tab === 'users'" class="space-y-6" x-cloak>
            <!-- Tambah User Baru -->
            <div class="bg-white rounded-hospital p-6 sm:p-8 border border-line shadow-xs max-w-3xl">
                <h3 class="text-base font-bold text-ink mb-4">Tambah Akun Staf Baru</h3>
                <form action="{{ route('admin.users.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Lengkap *</label>
                        <input type="text" name="name" required placeholder="Nama lengkap petugas faskes" class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email Login *</label>
                        <input type="email" name="email" required placeholder="email@puskesmasflow.local" class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Password *</label>
                        <input type="password" name="password" required minlength="6" class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Peran Akses (Role) *</label>
                        <select name="role" required class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-primary">
                            <option value="registrar">Petugas Pendaftaran (Loket)</option>
                            <option value="health_officer">Petugas Kesehatan (Meja Triase)</option>
                            <option value="doctor">Dokter / Petugas Poli</option>
                            <option value="admin">Administrator Sistem</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor Telepon</label>
                        <input type="text" name="phone" placeholder="08xxxxxxxxxx" class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-primary">
                    </div>
                    <div class="sm:col-span-2 flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-hover text-white font-semibold text-xs rounded-hospital shadow-xs transition-colors">
                            + Buat Akun Staf
                        </button>
                    </div>
                </form>
            </div>

            <!-- Daftar Akun Pengguna -->
            <div class="bg-white rounded-hospital border border-line shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-surface border-b border-line text-slate-600 font-semibold uppercase tracking-wider">
                            <tr>
                                <th class="p-3.5">Nama Staf</th>
                                <th class="p-3.5">Email Login</th>
                                <th class="p-3.5">Peran Akses</th>
                                <th class="p-3.5">Kontak</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line font-medium text-slate-700">
                            @foreach($users as $u)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="p-3.5 font-bold text-ink text-sm">{{ $u->name }}</td>
                                    <td class="p-3.5 font-mono text-slate-600">{{ $u->email }}</td>
                                    <td class="p-3.5">
                                        <span class="inline-block px-2.5 py-1 rounded-hospital text-[10px] font-bold uppercase
                                            {{ match($u->role) {
                                                'admin' => 'bg-purple-100 text-purple-800',
                                                'registrar' => 'bg-teal-100 text-teal-800',
                                                'health_officer' => 'bg-amber-100 text-amber-800',
                                                'doctor' => 'bg-blue-100 text-primary',
                                                default => 'bg-surface text-slate-800'
                                            } }}">
                                            {{ match($u->role) {
                                                'admin' => 'Administrator',
                                                'registrar' => 'Petugas Loket',
                                                'health_officer' => 'Petugas Triase',
                                                'doctor' => 'Dokter Poli',
                                                default => $u->role
                                            } }}
                                        </span>
                                    </td>
                                    <td class="p-3.5 text-slate-500 font-mono">{{ $u->phone ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
