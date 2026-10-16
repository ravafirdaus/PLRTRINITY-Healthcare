@extends('patient.layouts.app')

@section('title', 'Buat Reservasi — PuskesmasFlow')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <h1 class="text-xl sm:text-2xl font-extrabold text-ink tracking-tight">Buat Reservasi Poliklinik</h1>
        <p class="text-xs sm:text-sm text-slate-500 font-normal mt-0.5">Pilih poli layanan dan waktu kunjungan sesuai ketersediaan slot faskes.</p>
    </div>

    {{-- Patient Info Summary --}}
    <div class="bg-surface rounded-hospital p-4 border border-line">
        <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span>Data Pasien Terdaftar</span>
        </h3>
        <div class="grid grid-cols-2 gap-2 text-xs">
            <div>
                <span class="text-slate-400">Nama:</span>
                <span class="font-bold text-ink ml-1">{{ $patient->name }}</span>
            </div>
            <div>
                <span class="text-slate-400">NIK:</span>
                <span class="font-mono text-ink ml-1">{{ $patient->masked_nik }}</span>
            </div>
            <div>
                <span class="text-slate-400">No. HP:</span>
                <span class="font-medium text-ink ml-1">{{ $patient->phone }}</span>
            </div>
            <div>
                <span class="text-slate-400">BPJS:</span>
                <span class="font-medium text-ink ml-1">{{ $patient->bpjs_number ?? '-' }}</span>
            </div>
        </div>
        <a href="{{ route('patient.profile') }}" class="text-[11px] text-primary hover:text-primary-hover mt-2 inline-block font-semibold">Perbarui data diri &rarr;</a>
    </div>

    <div class="bg-white rounded-hospital border border-line shadow-xs p-6 sm:p-8" 
         x-data="{
            services: {{ Js::from($services) }},
            serviceId: '{{ old('service_id', '') }}',
            appointmentDate: '{{ old('appointment_date', '') }}',
            consultationType: '{{ old('consultation_type', 'in_person') }}',
            slots: [],
            loadingSlots: false,
            selectedSlot: '{{ old('slot_time', '') }}',
            get selectedService() {
                return this.services.find(s => s.id == this.serviceId);
            },
            get supportsTeleconsultation() {
                return this.selectedService ? !!this.selectedService.supports_teleconsultation : false;
            },
            onServiceChange() {
                if (!this.supportsTeleconsultation && this.consultationType === 'video') {
                    this.consultationType = 'in_person';
                }
                this.fetchSlots();
            },
            async fetchSlots() {
                if (!this.serviceId || !this.appointmentDate) {
                    this.slots = [];
                    return;
                }
                this.loadingSlots = true;
                this.selectedSlot = '{{ old('slot_time', '') }}';
                try {
                    const res = await fetch(`{{ route('patient.reservasi.slots') }}?service_id=${this.serviceId}&date=${this.appointmentDate}`);
                    const data = await res.json();
                    this.slots = data.slots || [];
                } catch (e) {
                    this.slots = [];
                }
                this.loadingSlots = false;
            }
         }"
         x-init="if (serviceId && appointmentDate) fetchSlots()">
        <form method="POST" action="{{ route('patient.reservasi.store') }}">
            @csrf

            <div class="space-y-5">
                <div>
                    <label for="service_id" class="block text-xs font-bold text-slate-700 uppercase mb-1">Poli / Layanan Tujuan <span class="text-red-500">*</span></label>
                    <select name="service_id" id="service_id" x-model="serviceId" @change="onServiceChange()"
                        class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-1 focus:ring-primary @error('service_id') border-red-500 @enderror" required>
                        <option value="">-- Pilih layanan poli --</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}">
                                {{ $service->name }} ({{ $service->code }}) {{ $service->supports_teleconsultation ? '— Mendukung Telekonsultasi' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('service_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="appointment_date" class="block text-xs font-bold text-slate-700 uppercase mb-1">Tanggal Kunjungan <span class="text-red-500">*</span></label>
                    <input type="date" name="appointment_date" id="appointment_date" x-model="appointmentDate" @change="fetchSlots()"
                        min="{{ date('Y-m-d') }}"
                        class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-1 focus:ring-primary @error('appointment_date') border-red-500 @enderror" required>
                    @error('appointment_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pilihan Metode Konsultasi -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Metode Konsultasi <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Tatap Muka -->
                        <label class="relative cursor-pointer">
                            <input type="radio" name="consultation_type" value="in_person" x-model="consultationType" class="sr-only peer">
                            <div class="p-3.5 rounded-hospital border border-line transition-all flex items-start gap-3 peer-checked:border-primary peer-checked:bg-primary-50/40 peer-checked:ring-1 peer-checked:ring-primary">
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 shrink-0 mt-0.5 peer-checked:bg-primary peer-checked:text-white">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <div>
                                    <span class="block text-xs font-bold text-ink">Tatap Muka di Puskesmas</span>
                                    <span class="block text-[11px] text-slate-500 mt-0.5">Datang langsung ke lokasi faskes dan ambil nomor loket.</span>
                                </div>
                            </div>
                        </label>

                        <!-- Telekonsultasi Video -->
                        <label class="relative" :class="{ 'cursor-pointer': supportsTeleconsultation, 'cursor-not-allowed opacity-60': !supportsTeleconsultation }">
                            <input type="radio" name="consultation_type" value="video" x-model="consultationType" :disabled="!supportsTeleconsultation" class="sr-only peer">
                            <div class="p-3.5 rounded-hospital border border-line transition-all flex items-start gap-3 peer-checked:border-primary peer-checked:bg-primary-50/40 peer-checked:ring-1 peer-checked:ring-primary"
                                 :class="{ 'border-slate-200 bg-slate-50': !supportsTeleconsultation }">
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 shrink-0 mt-0.5 peer-checked:bg-primary peer-checked:text-white">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-xs font-bold text-ink">Telekonsultasi Video</span>
                                        <span x-show="supportsTeleconsultation" class="text-[9px] font-bold px-1.5 py-0.2 bg-emerald-100 text-emerald-800 rounded">Online</span>
                                        <span x-show="!supportsTeleconsultation" class="text-[9px] font-semibold px-1.5 py-0.2 bg-slate-200 text-slate-600 rounded">Tidak Tersedia</span>
                                    </div>
                                    <span class="block text-[11px] text-slate-500 mt-0.5">Konsultasi jarak jauh dengan dokter langsung di browser Anda.</span>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div x-show="!supportsTeleconsultation && serviceId" class="mt-2 text-[11px] text-amber-700 bg-amber-50 border border-amber-200/80 rounded-hospital p-2.5 flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span>Poli ini hanya melayani pemeriksaan fisik langsung di faskes (belum mendukung telekonsultasi video).</span>
                    </div>

                    @error('consultation_type')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Waktu Kunjungan (Slot Waktu) <span class="text-red-500">*</span></label>
                    <div class="mt-2">
                        <template x-if="loadingSlots">
                            <p class="text-xs text-slate-500 flex items-center gap-2 py-2">
                                <span class="inline-block animate-spin w-4 h-4 border-2 border-primary border-t-transparent rounded-full"></span> Memeriksa slot waktu tersedia...
                            </p>
                        </template>
                        <template x-if="!loadingSlots && slots.length === 0 && serviceId && appointmentDate">
                            <p class="text-xs text-red-600 py-2">Tidak ada slot tersedia untuk tanggal ini.</p>
                        </template>
                        <template x-if="!loadingSlots && (!serviceId || !appointmentDate)">
                            <p class="text-xs text-slate-400 py-2">Pilih poli layanan dan tanggal kunjungan terlebih dahulu untuk melihat kuota slot waktu.</p>
                        </template>

                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2" x-show="!loadingSlots && slots.length > 0">
                            <template x-for="slot in slots" :key="slot.time">
                                <label class="cursor-pointer">
                                    <input type="radio" name="slot_time" 
                                           :value="slot.formatted_time || (slot.time && slot.time.length >= 5 ? slot.time.substring(0, 5) : slot.time)" 
                                           x-model="selectedSlot" 
                                           class="sr-only peer" 
                                           :disabled="(slot.is_available === false) || (slot.available === false)">
                                    <div class="text-center py-2 px-3 rounded-hospital border border-line text-xs font-semibold transition
                                        peer-checked:bg-primary peer-checked:text-white peer-checked:border-primary peer-checked:shadow-xs
                                        peer-disabled:bg-surface peer-disabled:text-slate-300 peer-disabled:cursor-not-allowed peer-disabled:border-line
                                        hover:border-primary/40">
                                        <span x-text="slot.formatted_time || slot.time"></span>
                                        <span x-show="(slot.is_available === false) || (slot.available === false)" class="block text-[10px] font-normal">Penuh</span>
                                    </div>
                                </label>
                            </template>
                        </div>
                    </div>
                    @error('slot_time')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-xs font-bold text-slate-700 uppercase mb-1">Keluhan / Catatan Pasien <span class="text-slate-400 text-xs font-normal">(opsional)</span></label>
                    <textarea name="notes" id="notes" rows="3"
                        class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-1 focus:ring-primary @error('notes') border-red-500 @enderror"
                        placeholder="Tuliskan keluhan singkat yang dirasakan...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Pembiayaan Otomatis Berdasarkan Kartu Pasien Digital (e-KIP) -->
                <div class="pt-4 border-t border-line">
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-slate-700 uppercase">
                            Transparansi Subsidi &amp; Biaya Layanan
                        </label>
                        <span class="inline-flex items-center gap-1 text-[10px] text-teal-700 bg-teal-50 px-2 py-0.5 rounded-full border border-teal-200 font-medium">
                            <svg class="w-3 h-3 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Otomatis dari Kartu e-KIP
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 mb-3">
                        Status pembiayaan Anda telah otomatis terdaftar di <strong>Kartu Pasien Digital (e-KIP)</strong> sejak registrasi akun. Anda tidak perlu memilih golongan biaya lagi saat reservasi.
                    </p>

                    @if($patient->is_subsidized)
                        <!-- Subsidi Penuh (Bebas Biaya Rp 0) -->
                        <div class="p-4 rounded-hospital border border-emerald-300 bg-emerald-50/50 shadow-xs space-y-2.5">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2 border-b border-emerald-200/80">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-2xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <span class="text-xs font-bold text-emerald-950 block">Golongan Layanan: Subsidi Penuh (PBI / KIS)</span>
                                        <span class="text-[11px] text-emerald-800 font-mono">No. e-KIP: <strong>{{ $patient->member_number }}</strong></span>
                                    </div>
                                </div>
                                <div class="shrink-0">
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-black bg-emerald-500 text-white shadow-2xs">
                                        Bebas Biaya (Rp 0)
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-start gap-2 text-xs text-emerald-900 leading-relaxed">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <div>
                                    Pelayanan ini <strong>Bebas Biaya (Rp 0)</strong> secara transparan tanpa iuran bulanan dan tanpa tagihan sama sekali. Cukup tunjukkan kartu e-KIP atau nomor peserta Anda saat tiba di loket Puskesmas.
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Mandiri / Reguler (Menengah ke Atas) -->
                        <div class="p-4 rounded-hospital border border-sky-300 bg-sky-50/50 shadow-xs space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2 border-b border-sky-200/80">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-sky-600 text-white flex items-center justify-center shrink-0 shadow-2xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </div>
                                    <div>
                                        <span class="text-xs font-bold text-sky-950 block">Golongan Layanan: Mandiri / Reguler</span>
                                        <span class="text-[11px] text-sky-800 font-mono">No. e-KIP: <strong>{{ $patient->member_number }}</strong></span>
                                    </div>
                                </div>
                                <div class="shrink-0">
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-sky-200 text-sky-950 border border-sky-300 shadow-2xs">
                                        Pasien Umum / Non-Subsidi
                                    </span>
                                </div>
                            </div>

                            <!-- Rincian Biaya Transparan -->
                            <div class="bg-white rounded-lg p-3 border border-sky-200 text-xs space-y-1.5">
                                <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider block">Estimasi Rincian Retribusi &amp; Layanan:</span>
                                <div class="flex justify-between text-slate-600">
                                    <span>Retribusi Administrasi &amp; Pendaftaran:</span>
                                    <span class="font-mono font-semibold text-ink">Rp 10.000</span>
                                </div>
                                <div class="flex justify-between text-slate-600">
                                    <span>Jasa Konsultasi Dokter &amp; Skrining Awal:</span>
                                    <span class="font-mono font-semibold text-ink">Rp 15.000</span>
                                </div>
                                <div class="flex justify-between pt-1.5 border-t border-slate-100 font-bold text-sky-900">
                                    <span>Total Estimasi Biaya Kunjungan:</span>
                                    <span class="font-mono text-sm text-sky-950">Rp 25.000</span>
                                </div>
                            </div>

                            <div class="text-[11px] text-slate-600 flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-sky-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                <span>Opsi pembayaran: <em>On-the-spot</em> di loket Puskesmas (Tunai / Debit) atau Digital (QRIS) saat Anda tiba di lokasi.</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <button type="submit" class="w-full mt-6 bg-primary hover:bg-primary-hover text-white font-semibold py-2.5 px-4 rounded-hospital transition-colors shadow-xs text-xs sm:text-sm">
                Konfirmasi & Buat Reservasi &rarr;
            </button>
        </form>
    </div>
</div>
@endsection
