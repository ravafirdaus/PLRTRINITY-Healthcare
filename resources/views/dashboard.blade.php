<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="font-extrabold text-xl sm:text-2xl text-ink tracking-tight">
                    {{ __('Dashboard Portal Petugas') }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 font-normal mt-0.5">
                    Selamat datang di sistem manajemen antrean dan pelayanan PuskesmasFlow.
                </p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-hospital text-xs font-semibold bg-primary-50 text-primary border border-primary/20">
                <span class="w-2 h-2 rounded-full bg-accent animate-pulse"></span>
                <span>Aktif Bertugas</span>
            </span>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Quick Workstation Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @if(in_array(auth()->user()->role, ['admin', 'registrar']))
                <a href="{{ route('registrar.dashboard') }}" class="bg-white p-5 rounded-hospital border border-line shadow-xs hover:border-primary/40 hover:shadow-sm transition-all group">
                    <div class="w-10 h-10 rounded-hospital bg-primary-50 flex items-center justify-center text-primary mb-3 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="font-bold text-ink text-sm">Loket Pendaftaran</h3>
                    <p class="text-xs text-slate-500 mt-1">Registrasi walk-in dan check-in reservasi online.</p>
                </a>
            @endif

            @if(in_array(auth()->user()->role, ['admin', 'health_officer']))
                <a href="{{ route('triage.index') }}" class="bg-white p-5 rounded-hospital border border-line shadow-xs hover:border-primary/40 hover:shadow-sm transition-all group">
                    <div class="w-10 h-10 rounded-hospital bg-amber-50 flex items-center justify-center text-amber-600 mb-3 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="font-bold text-ink text-sm">Meja Triase Klinis</h3>
                    <p class="text-xs text-slate-500 mt-1">Pemeriksaan tanda vital & prioritas ATS.</p>
                </a>
            @endif

            @if(in_array(auth()->user()->role, ['admin', 'doctor']))
                <a href="{{ route('doctor.dashboard') }}" class="bg-white p-5 rounded-hospital border border-line shadow-xs hover:border-primary/40 hover:shadow-sm transition-all group">
                    <div class="w-10 h-10 rounded-hospital bg-emerald-50 flex items-center justify-center text-accent mb-3 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    </div>
                    <h3 class="font-bold text-ink text-sm">Pelayanan Poli Dokter</h3>
                    <p class="text-xs text-slate-500 mt-1">Panggil antrean dan konsultasi rawat jalan.</p>
                </a>
            @endif

            <a href="{{ route('public.queue') }}" target="_blank" class="bg-white p-5 rounded-hospital border border-line shadow-xs hover:border-primary/40 hover:shadow-sm transition-all group">
                <div class="w-10 h-10 rounded-hospital bg-sky-50 flex items-center justify-center text-primary mb-3 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="font-bold text-ink text-sm">Layar Monitor Antrean</h3>
                <p class="text-xs text-slate-500 mt-1">Buka monitor antrean publik ruang tunggu.</p>
            </a>
        </div>

        <!-- Info Card -->
        <div class="bg-white rounded-hospital p-6 sm:p-8 border border-line shadow-xs space-y-3">
            <h2 class="text-base font-bold text-ink">Informasi Standar Operasional Faskes</h2>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                PuskesmasFlow dirancang untuk memastikan integrasi terpadu mulai dari registrasi loket, penilaian kegawatdaruratan pada Meja Triase Australasian Triage Scale (ATS), pemanggilan antrean dokter per poliklinik, hingga dokumentasi disposisi kepulangan pasien.
            </p>
            <div class="pt-3 border-t border-line flex flex-wrap gap-4 text-xs text-slate-500">
                <span>&bull; Standar Pelayanan Pasien Terpadu</span>
                <span>&bull; Enkripsi & Proteksi NIK Pasien</span>
                <span>&bull; Monitoring Beban Kerja Terintegrasi</span>
            </div>
        </div>
    </div>
</x-app-layout>
