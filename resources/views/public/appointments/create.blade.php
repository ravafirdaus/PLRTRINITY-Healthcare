<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reservasi Jadwal Pasien — PuskesmasFlow</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface text-ink antialiased selection:bg-primary selection:text-white font-sans min-h-screen flex flex-col justify-between">
    <!-- Top Institutional Accent Bar -->
    <div class="h-1 bg-gradient-to-r from-primary to-accent"></div>

    <!-- Header -->
    <header class="bg-white border-b border-line sticky top-0 z-40">
        <div class="max-w-5xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-hospital bg-primary flex items-center justify-center text-white shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div class="text-left">
                    <span class="text-base font-extrabold text-ink tracking-tight">Puskesmas<span class="text-primary">Flow</span></span>
                </div>
            </a>
            <div class="flex items-center gap-4 text-xs font-semibold">
                <a href="{{ route('public.queue') }}" class="text-slate-600 hover:text-primary">Monitor Antrean</a>
                <a href="{{ route('public.appointments.search') }}" class="text-slate-600 hover:text-primary">Cek Tiket Saya</a>
            </div>
        </div>
    </header>

    <main class="py-10 flex-1">
        <div class="max-w-3xl mx-auto px-4 sm:px-6">
            <div class="mb-8 text-center">
                <span class="px-3 py-1 rounded-full bg-primary-50 text-primary text-xs font-bold uppercase tracking-wider">Pendaftaran Mandiri Pasien</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-ink mt-2">Reservasi Jadwal Kunjungan Poli</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Pilih poliklinik tujuan, tanggal, dan slot waktu untuk mengurangi antrean di ruang tunggu.</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-hospital bg-red-50 border border-red-200 text-red-800 text-sm">
                    <div class="font-bold mb-1 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        Mohon periksa kembali formulir:
                    </div>
                    <ul class="list-disc ms-8 text-xs space-y-1">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('public.appointments.store') }}" method="POST"
                  x-data="appointmentForm()" x-init="init()"
                  class="bg-white rounded-hospital p-6 sm:p-10 border border-line shadow-xs space-y-8">
                @csrf

                <!-- Langkah 1: Pilih Layanan & Tanggal -->
                <div>
                    <h2 class="text-sm font-bold text-ink uppercase tracking-wider mb-4 pb-2 border-b border-line flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-primary text-white flex items-center justify-center text-[10px]">1</span>
                        Pilih Poli & Tanggal Kunjungan
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Poli Tujuan *</label>
                            <select name="service_id" x-model="selectedService" @change="fetchSlots()" required
                                    class="w-full rounded-hospital border-line focus:border-primary focus:ring-1 focus:ring-primary text-sm">
                                <option value="">-- Pilih Poli Layanan --</option>
                                @foreach($services as $svc)
                                    <option value="{{ $svc->id }}" {{ old('service_id') == $svc->id ? 'selected' : '' }}>
                                        {{ $svc->name }} ({{ $svc->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tanggal Rencana Kunjungan *</label>
                            <input type="date" name="appointment_date" x-model="selectedDate" @change="fetchSlots()"
                                   min="{{ $minDate }}" max="{{ $maxDate }}" value="{{ old('appointment_date', $minDate) }}" required
                                   class="w-full rounded-hospital border-line focus:border-primary focus:ring-1 focus:ring-primary text-sm">
                        </div>
                    </div>
                </div>

                <!-- Langkah 2: Pilih Slot Waktu -->
                <div>
                    <h2 class="text-sm font-bold text-ink uppercase tracking-wider mb-4 pb-2 border-b border-line flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-primary text-white flex items-center justify-center text-[10px]">2</span>
                        Pilih Slot Waktu Kunjungan (20 Menit per Slot)
                    </h2>

                    <input type="hidden" name="slot_time" :value="selectedSlot" required>

                    <!-- Loading state -->
                    <div x-show="loadingSlots" class="p-8 text-center text-slate-500 text-xs">
                        <div class="inline-block animate-spin w-5 h-5 border-2 border-primary border-t-transparent rounded-full mb-2"></div>
                        <p>Memeriksa ketersediaan kuota slot...</p>
                    </div>

                    <!-- Slots Grid -->
                    <div x-show="!loadingSlots">
                        <template x-if="slots.length === 0">
                            <div class="p-6 text-center text-slate-500 bg-surface rounded-hospital border border-line text-xs">
                                <span x-show="!selectedService">Silakan pilih Poli Layanan terlebih dahulu.</span>
                                <span x-show="selectedService">Tidak ada slot pelayanan yang tersedia pada tanggal ini.</span>
                            </div>
                        </template>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5" x-show="slots.length > 0">
                            <template x-for="slot in slots" :key="slot.time">
                                <button type="button"
                                        @click="if(slot.is_available) selectedSlot = slot.formatted_time"
                                        :disabled="!slot.is_available"
                                        :class="{
                                            'bg-primary text-white border-primary shadow-xs': selectedSlot === slot.formatted_time,
                                            'bg-white text-slate-700 border-line hover:border-primary/50': slot.is_available && selectedSlot !== slot.formatted_time,
                                            'bg-surface text-slate-300 border-line cursor-not-allowed': !slot.is_available
                                        }"
                                        class="p-2.5 rounded-hospital border text-center transition-all flex flex-col items-center justify-center">
                                    <span class="font-mono font-bold text-xs" x-text="slot.formatted_time"></span>
                                    <span class="text-[10px] mt-0.5"
                                          :class="selectedSlot === slot.formatted_time ? 'text-primary-50' : 'text-slate-400'"
                                          x-text="slot.is_available ? (slot.remaining + ' sisa') : 'Penuh'"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Langkah 3: Identitas Pasien -->
                <div>
                    <h2 class="text-sm font-bold text-ink uppercase tracking-wider mb-4 pb-2 border-b border-line flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-primary text-white flex items-center justify-center text-[10px]">3</span>
                        Data Identitas Pasien
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor Induk Kependudukan (NIK) *</label>
                            <input type="text" name="nik" maxlength="16" required placeholder="16 digit sesuai KTP" value="{{ old('nik') }}"
                                   class="w-full rounded-hospital border-line font-mono focus:border-primary focus:ring-1 focus:ring-primary text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Lengkap Pasien *</label>
                            <input type="text" name="name" required placeholder="Nama lengkap" value="{{ old('name') }}"
                                   class="w-full rounded-hospital border-line focus:border-primary focus:ring-1 focus:ring-primary text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor WhatsApp / HP Aktif *</label>
                            <input type="text" name="phone" required placeholder="08xxxxxxxxxx" value="{{ old('phone') }}"
                                   class="w-full rounded-hospital border-line focus:border-primary focus:ring-1 focus:ring-primary text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tanggal Lahir Pasien *</label>
                            <input type="date" name="date_of_birth" required value="{{ old('date_of_birth') }}"
                                   class="w-full rounded-hospital border-line focus:border-primary focus:ring-1 focus:ring-primary text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Jenis Kelamin *</label>
                            <select name="gender" required class="w-full rounded-hospital border-line focus:border-primary focus:ring-1 focus:ring-primary text-sm">
                                <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor Kartu BPJS (Opsional)</label>
                            <input type="text" name="bpjs_number" placeholder="Nomor BPJS jika memiliki" value="{{ old('bpjs_number') }}"
                                   class="w-full rounded-hospital border-line font-mono focus:border-primary focus:ring-1 focus:ring-primary text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Alamat Singkat</label>
                            <input type="text" name="address" placeholder="Desa / Kelurahan domisili" value="{{ old('address') }}"
                                   class="w-full rounded-hospital border-line focus:border-primary focus:ring-1 focus:ring-primary text-sm">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Keluhan Singkat (Opsional)</label>
                            <input type="text" name="notes" placeholder="Contoh: batuk pilek sejak 2 hari, kontrol gigi rutin" value="{{ old('notes') }}"
                                   class="w-full rounded-hospital border-line focus:border-primary focus:ring-1 focus:ring-primary text-sm">
                        </div>
                    </div>
                </div>

                <!-- Langkah 4: Transparansi Subsidi & Pembayaran Berdasarkan Golongan -->
                <div>
                    <h2 class="text-sm font-bold text-ink uppercase tracking-wider mb-4 pb-2 border-b border-line flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-primary text-white flex items-center justify-center text-[10px]">4</span>
                        Transparansi Subsidi &amp; Pembayaran Berdasarkan Golongan
                    </h2>

                    <p class="text-xs text-slate-500 mb-4">
                        Pilih kategori pembiayaan pasien Anda. Puskesmas menjamin kepastian status biaya tanpa biaya siluman.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Pilihan Subsidi Penuh (Menengah ke Bawah) -->
                        <label class="relative block cursor-pointer">
                            <input type="radio" name="patient_tier" value="subsidi" x-model="patientTier" class="sr-only peer">
                            <div class="p-4 rounded-hospital border transition-all h-full flex flex-col justify-between"
                                 :class="patientTier === 'subsidi' ? 'border-emerald-500 bg-emerald-50/40 ring-1 ring-emerald-500 shadow-xs' : 'border-line hover:border-slate-300 bg-white'">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-bold text-ink">Subsidi Penuh</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            Bebas Biaya (Rp 0)
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-600 font-medium">Golongan Menengah ke Bawah / KIS / BPJS PBI</p>
                                    
                                    <div class="mt-3 p-3 rounded-hospital bg-white border border-emerald-200 text-xs space-y-1.5 shadow-2xs">
                                        <div class="flex items-center gap-1.5 font-bold text-emerald-800">
                                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            <span>Status: Bebas Biaya (Rp 0)</span>
                                        </div>
                                        <p class="text-[11px] text-slate-600 leading-snug">
                                            Ditampilkan status <strong>"Bebas Biaya (Rp 0)"</strong> secara transparan tanpa iuran bulanan dan tanpa tagihan sama sekali.
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-3 text-[10px] text-emerald-700 font-semibold flex items-center gap-2">
                                    <span>✓ Tanpa iuran bulanan</span>
                                    <span>✓ Tanpa tagihan sama sekali</span>
                                </div>
                            </div>
                        </label>

                        <!-- Pilihan Mandiri/Reguler (Menengah ke Atas) -->
                        <label class="relative block cursor-pointer">
                            <input type="radio" name="patient_tier" value="mandiri" x-model="patientTier" class="sr-only peer">
                            <div class="p-4 rounded-hospital border transition-all h-full flex flex-col justify-between"
                                 :class="patientTier === 'mandiri' ? 'border-sky-500 bg-sky-50/40 ring-1 ring-sky-500 shadow-xs' : 'border-line hover:border-slate-300 bg-white'">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-bold text-ink">Mandiri / Reguler</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-100 text-sky-800 border border-sky-300">
                                            Pasien Umum
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-600 font-medium">Golongan Menengah ke Atas / Non-Subsidi</p>

                                    <!-- Estimasi Rincian Biaya -->
                                    <div class="mt-3 p-3 rounded-hospital bg-white border border-sky-200 text-xs space-y-1 shadow-2xs">
                                        <div class="flex justify-between text-slate-600 text-[11px]">
                                            <span>Retribusi Pendaftaran:</span>
                                            <span class="font-mono font-semibold text-ink">Rp 10.000</span>
                                        </div>
                                        <div class="flex justify-between text-slate-600 text-[11px]">
                                            <span>Jasa Konsultasi &amp; Skrining:</span>
                                            <span class="font-mono font-semibold text-ink">Rp 15.000</span>
                                        </div>
                                        <div class="flex justify-between pt-1 border-t border-slate-100 font-bold text-sky-900 text-xs">
                                            <span>Total Estimasi Dasar:</span>
                                            <span class="font-mono">Rp 25.000</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Opsi Pembayaran Mandiri -->
                                <div class="mt-3 pt-2 border-t border-sky-100 text-[11px]">
                                    <span class="font-bold text-slate-700 block mb-1">Opsi Metode Pembayaran:</span>
                                    <div class="flex items-center gap-3">
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                            <input type="radio" name="payment_method" value="loket" x-model="paymentMethod" class="w-3.5 h-3.5 text-sky-600 focus:ring-sky-500">
                                            <span class="text-slate-700">On-the-spot di Loket</span>
                                        </label>
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                            <input type="radio" name="payment_method" value="digital" x-model="paymentMethod" class="w-3.5 h-3.5 text-sky-600 focus:ring-sky-500">
                                            <span class="text-slate-700">Digital (QRIS)</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="pt-4 border-t border-line flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-xs text-slate-500 text-center sm:text-left">
                        Pastikan data NIK benar untuk verifikasi identitas saat check-in faskes.
                    </p>
                    <button type="submit"
                            :disabled="!selectedSlot"
                            class="w-full sm:w-auto px-6 py-2.5 bg-primary hover:bg-primary-hover disabled:bg-slate-200 disabled:text-slate-400 disabled:cursor-not-allowed text-white font-semibold text-xs sm:text-sm rounded-hospital shadow-xs transition-all">
                        Simpan & Terbitkan Reservasi &rarr;
                    </button>
                </div>
            </form>
        </div>
    </main>

    <!-- Institutional Footer -->
    <footer class="py-4 border-t border-line text-center text-xs text-slate-400 bg-white">
        &copy; {{ date('Y') }} PuskesmasFlow &bull; Sistem Layanan Faskes Terpadu
    </footer>

    <script>
        function appointmentForm() {
            return {
                selectedService: '{{ old('service_id', '') }}',
                selectedDate: '{{ old('appointment_date', $minDate) }}',
                selectedSlot: '{{ old('slot_time', '') }}',
                patientTier: '{{ old('bpjs_number') ? 'subsidi' : 'subsidi' }}',
                paymentMethod: 'loket',
                slots: [],
                loadingSlots: false,
                init() {
                    if (this.selectedService && this.selectedDate) {
                        this.fetchSlots();
                    }
                },
                async fetchSlots() {
                    if (!this.selectedService || !this.selectedDate) {
                        this.slots = [];
                        return;
                    }
                    this.loadingSlots = true;
                    try {
                        const url = `{{ route('public.appointments.check_slots') }}?service_id=${this.selectedService}&date=${this.selectedDate}`;
                        const res = await fetch(url);
                        const data = await res.json();
                        this.slots = data.slots || [];
                    } catch (e) {
                        console.error('Failed to fetch slots', e);
                        this.slots = [];
                    } finally {
                        this.loadingSlots = false;
                    }
                }
            }
        }
    </script>
</body>
</html>
