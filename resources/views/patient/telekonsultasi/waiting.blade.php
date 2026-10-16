@extends('patient.layouts.app')

@section('title', 'Ruang Tunggu Virtual — PuskesmasFlow')

@section('content')
<div class="max-w-xl mx-auto space-y-6"
     x-data="{
        status: '{{ $teleconsultation->status }}',
        doctorReady: {{ in_array($teleconsultation->status, ['doctor_ready', 'in_progress']) ? 'true' : 'false' }},
        checking: false,
        lastChecked: new Date().toLocaleTimeString('id-ID'),
        timerId: null,
        async checkStatus() {
            try {
                this.checking = true;
                const res = await fetch('{{ route('patient.telekonsultasi.status', $teleconsultation) }}');
                const data = await res.json();
                this.status = data.status;
                this.doctorReady = !!data.can_join || !!data.doctor_ready;
                this.lastChecked = new Date().toLocaleTimeString('id-ID');
                
                // If doctor is ready, redirect to room automatically after short delay
                if (this.doctorReady) {
                    window.location.href = '{{ route('patient.telekonsultasi.room', $teleconsultation) }}';
                }
            } catch (e) {
                console.error('Status check error:', e);
            } finally {
                this.checking = false;
            }
        },
        startPolling() {
            this.timerId = setInterval(() => this.checkStatus(), 3000);
        }
     }"
     x-init="startPolling()"
     x-on:beforeunload.window="if (timerId) clearInterval(timerId)">

    <!-- Top Card: Live Waiting Indicator -->
    <div class="bg-white rounded-hospital border border-line p-8 shadow-xs text-center space-y-5">
        <!-- Animated Visual Icon -->
        <div class="relative w-20 h-20 mx-auto flex items-center justify-center">
            <span class="absolute inline-flex h-full w-full rounded-full bg-primary/20 animate-ping" x-show="!doctorReady"></span>
            <div class="relative w-16 h-16 rounded-full flex items-center justify-center text-white shadow-md transition-colors"
                 :class="doctorReady ? 'bg-emerald-600' : 'bg-primary'">
                <template x-if="!doctorReady">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
                <template x-if="doctorReady">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </template>
            </div>
        </div>

        <div>
            <template x-if="!doctorReady">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-primary block">Ruang Tunggu Virtual</span>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-ink mt-1">Menunggu Dokter Memulai Sesi</h1>
                    <p class="text-xs text-slate-500 mt-2 max-w-md mx-auto leading-relaxed">
                        Anda sudah berada di antrean telekonsultasi. Tetap buka halaman ini, sistem akan otomatis menghubungkan Anda begitu dokter siap di ruang periksa.
                    </p>
                </div>
            </template>
            <template x-if="doctorReady">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-emerald-600 block animate-pulse">Dokter Telah Siap!</span>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-emerald-800 mt-1">Menghubungkan ke Ruang Video...</h1>
                    <p class="text-xs text-slate-600 mt-2">
                        Sedang mengalihkan Anda ke ruang konsultasi video. Jika tidak berpindah otomatis, klik tombol di bawah.
                    </p>
                </div>
            </template>
        </div>

        <!-- Consultation Metadata -->
        <div class="bg-surface rounded-hospital p-4 border border-line text-xs grid grid-cols-2 gap-3 text-left">
            <div>
                <span class="text-slate-400 block text-[11px]">Poli Layanan:</span>
                <span class="font-bold text-ink">{{ $teleconsultation->service->name }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Dokter Pemeriksa:</span>
                <span class="font-semibold text-ink">{{ $teleconsultation->doctor?->name ?? 'Akan segera hadir' }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Jadwal Konsultasi:</span>
                <span class="font-mono text-ink">{{ $teleconsultation->scheduled_at ? $teleconsultation->scheduled_at->format('H:i') : '-' }} WIB</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Status Ruang:</span>
                <span class="font-bold text-primary" x-text="doctorReady ? 'Dokter Siap' : 'Menunggu Panggilan'"></span>
            </div>
        </div>

        <!-- Action / Manual Button -->
        <div class="pt-2">
            <template x-if="doctorReady">
                <a href="{{ route('patient.telekonsultasi.room', $teleconsultation) }}"
                   class="inline-flex items-center justify-center gap-2 w-full py-3 px-4 rounded-hospital bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md transition animate-pulse">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    <span>Masuk ke Ruang Konsultasi Sekarang</span>
                </a>
            </template>
            <template x-if="!doctorReady">
                <div class="flex items-center justify-center gap-2 text-xs text-slate-400">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Pemeriksaan status otomatis aktif &bull; Terakhir dicek: <span x-text="lastChecked"></span></span>
                </div>
            </template>
        </div>
    </div>

    <!-- Quick Checklist While Waiting -->
    <div class="bg-surface rounded-hospital border border-line p-5 text-xs space-y-3">
        <h3 class="font-bold text-ink text-xs uppercase tracking-wider flex items-center gap-1.5">
            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span>Persiapan Selagi Menunggu Dokter:</span>
        </h3>
        <ul class="space-y-1.5 text-slate-600 list-disc list-inside leading-relaxed">
            <li>Pastikan Anda berada di ruangan yang tenang dan memiliki pencahayaan cukup.</li>
            <li>Siapkan catatan keluhan utama, riwayat obat yang sedang dikonsumsi, atau riwayat alergi.</li>
            <li>Bila menggunakan ponsel, posisikan perangkat secara stabil agar dokter dapat melihat dengan jelas.</li>
            <li>Gunakan headset atau earphone untuk kualitas suara yang lebih jernih dan menjaga privasi konsultasi.</li>
        </ul>
    </div>

    <!-- Navigation links -->
    <div class="flex items-center justify-between text-xs pt-2">
        <a href="{{ route('patient.telekonsultasi.show', $teleconsultation) }}" class="text-slate-500 hover:text-ink">
            &larr; Lihat Ketentuan & Detail
        </a>
        <a href="{{ route('patient.dashboard') }}" class="text-primary hover:text-primary-hover font-semibold">
            Kembali ke Dashboard Pasien
        </a>
    </div>
</div>
@endsection
