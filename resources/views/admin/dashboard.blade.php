<x-app-layout>
    <x-slot name="header">
        <h1 class="font-extrabold text-xl sm:text-2xl text-ink tracking-tight">
            {{ __('Pengaturan Sistem & Administrasi Puskesmas') }}
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 font-normal mt-0.5">
            Kelola konfigurasi slot waktu faskes, poliklinik aktif, dan hak akses pengguna.
        </p>
    </x-slot>

    <div class="space-y-6" x-data="{ tab: 'settings' }">
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
            <button @click="tab = 'users'"
                    :class="tab === 'users' ? 'bg-primary-50 text-primary border-primary/30 shadow-xs font-bold' : 'text-slate-600 hover:text-ink border-transparent hover:bg-surface font-medium'"
                    class="py-2 px-3.5 border rounded-hospital text-xs sm:text-sm transition-all">
                3. Manajemen Akun & Hak Akses
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

        <!-- TAB 3: MANAJEMEN AKUN & HAK AKSES -->
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
