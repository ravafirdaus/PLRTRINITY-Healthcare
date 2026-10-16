<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tiket Reservasi {{ $appointment->appointment_number }} — PuskesmasFlow</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface text-ink antialiased selection:bg-primary selection:text-white py-10 font-sans">
    <div class="max-w-md mx-auto px-4">
        <!-- Back Link (hide on print) -->
        <div class="mb-5 flex items-center justify-between print:hidden">
            <a href="{{ route('home') }}" class="text-xs font-semibold text-slate-500 hover:text-ink flex items-center gap-1">
                &larr; Beranda PuskesmasFlow
            </a>
            <button onclick="window.print()" class="px-3.5 py-1.5 rounded-hospital bg-white border border-line text-slate-700 text-xs font-semibold shadow-xs hover:bg-surface flex items-center gap-1.5">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Tiket
            </button>
        </div>

        @if(session('success'))
            <div class="mb-5 p-3.5 rounded-hospital bg-emerald-50 border border-emerald-200 text-accent text-xs font-semibold flex items-center gap-2 print:hidden">
                <svg class="w-4 h-4 text-accent shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Ticket Card -->
        <div class="bg-white rounded-hospital border border-line shadow-sm overflow-hidden relative">
            <!-- Header bar -->
            <div class="bg-primary text-white p-6 text-center">
                <div class="text-[10px] font-bold uppercase tracking-widest text-primary-50">BUKTI RESERVASI ONLINE</div>
                <h1 class="text-xl font-extrabold mt-1 tracking-tight">PuskesmasFlow</h1>
                <p class="text-xs text-primary-100">Sistem Pelayanan Terpadu Pasien</p>
            </div>

            <div class="p-6 space-y-5">
                <!-- Appointment Number Box -->
                <div class="text-center p-4 rounded-hospital bg-primary-50 border border-primary/20">
                    <div class="text-xs font-bold uppercase tracking-wider text-primary">Nomor Reservasi</div>
                    <div class="text-2xl sm:text-3xl font-black font-mono text-ink mt-1 select-all tracking-wider">
                        {{ $appointment->appointment_number }}
                    </div>
                    <div class="mt-2">
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                            {{ match($appointment->status) {
                                'booked' => 'bg-amber-50 text-amber-700 border border-amber-200',
                                'checked_in' => 'bg-emerald-50 text-accent border border-emerald-200',
                                'completed' => 'bg-sky-50 text-primary border border-sky-200',
                                'no_show' => 'bg-surface text-slate-800 border border-line',
                                'cancelled' => 'bg-red-50 text-red-600 border border-red-200',
                                default => 'bg-surface text-slate-800 border border-line'
                            } }}">
                            Status: {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                        </span>
                    </div>
                </div>

                <!-- Schedule Details -->
                <div class="space-y-2.5 text-xs">
                    <div class="flex justify-between pb-2 border-b border-line">
                        <span class="text-slate-400">Poli Tujuan:</span>
                        <span class="font-bold text-ink">{{ $appointment->service->name }} ({{ $appointment->service->code }})</span>
                    </div>
                    <div class="flex justify-between pb-2 border-b border-line">
                        <span class="text-slate-400">Metode Pelayanan:</span>
                        <span class="font-semibold text-ink">
                            {{ $appointment->isVideo() ? 'Telekonsultasi Video' : 'Tatap Muka di Puskesmas' }}
                        </span>
                    </div>
                    <div class="flex justify-between pb-2 border-b border-line">
                        <span class="text-slate-400">Tanggal Kunjungan:</span>
                        <span class="font-medium text-ink">{{ $appointment->appointment_date->translatedFormat('l, d F Y') }}</span>
                    </div>
                    <div class="flex justify-between pb-2 border-b border-line">
                        <span class="text-slate-400">Slot Waktu:</span>
                        <span class="font-bold text-primary font-mono text-sm">{{ substr($appointment->slot_time, 0, 5) }} WIB</span>
                    </div>
                    <div class="flex justify-between pb-2 border-b border-line">
                        <span class="text-slate-400">Nama Pasien:</span>
                        <span class="font-bold text-ink">{{ $appointment->patient->name }}</span>
                    </div>
                    <div class="flex justify-between pb-2 border-b border-line">
                        <span class="text-slate-400">NIK Pasien:</span>
                        <span class="font-mono text-slate-600">{{ substr($appointment->patient->nik, 0, 6) }}******{{ substr($appointment->patient->nik, -4) }}</span>
                    </div>
                </div>

                <!-- Transparansi Subsidi & Status Biaya -->
                <div class="rounded-hospital p-3.5 border {{ $appointment->patient->bpjs_number ? 'bg-emerald-50/70 border-emerald-200 text-emerald-900' : 'bg-sky-50/70 border-sky-200 text-sky-900' }} space-y-1.5 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-[11px] uppercase tracking-wider {{ $appointment->patient->bpjs_number ? 'text-emerald-800' : 'text-sky-800' }}">
                            Transparansi Subsidi &amp; Biaya
                        </span>
                        @if($appointment->patient->bpjs_number)
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                Bebas Biaya (Rp 0)
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-100 text-sky-800 border border-sky-300">
                                Mandiri / Reguler
                            </span>
                        @endif
                    </div>

                    @if($appointment->patient->bpjs_number)
                        <p class="text-[11px] text-emerald-800 leading-snug">
                            Status <strong>"Bebas Biaya (Rp 0)"</strong> — Pasien dijamin bebas biaya tanpa iuran bulanan dan tanpa tagihan sama sekali saat berkunjung ke faskes.
                        </p>
                    @else
                        <div class="text-[11px] text-slate-700 space-y-0.5 pt-1">
                            <p class="font-semibold text-slate-800">Estimasi Rincian Biaya: Rp 25.000 (Pendaftaran Rp 10.000 + Konsultasi Rp 15.000)</p>
                            <p class="text-[10px] text-slate-500">Opsi pembayaran dapat dilakukan <em>on-the-spot</em> di loket Puskesmas (Tunai/Debit) atau Digital (QRIS).</p>
                        </div>
                    @endif
                </div>

                @if($appointment->isVideo())
                    <div class="p-3.5 rounded-hospital bg-primary-50 border border-primary/20 text-xs space-y-2">
                        <p class="font-bold text-primary flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            Sesi Telekonsultasi Terjadwal
                        </p>
                        <p class="text-slate-600">
                            Untuk alasan keamanan dan privasi medis, ruang video dapat diakses melalui Portal Pasien terdaftar.
                        </p>
                        <a href="{{ route('patient.login') }}" class="inline-block px-3 py-1.5 rounded-hospital bg-primary hover:bg-primary-hover text-white font-semibold text-[11px] transition shadow-xs">
                            Masuk ke Portal Pasien &rarr;
                        </a>
                    </div>
                @endif

                <!-- Instructions -->
                <div class="p-3.5 rounded-hospital bg-surface border border-line text-[11px] text-slate-600 space-y-1 leading-relaxed">
                    <div class="font-bold text-slate-800">Petunjuk Kedatangan Faskes:</div>
                    <p>&bull; Harap tiba di loket pendaftaran 10 menit sebelum slot waktu Anda.</p>
                    <p>&bull; Tunjukkan bukti reservasi ini atau sebutkan NIK kepada petugas loket untuk check-in.</p>
                    <p>&bull; Toleransi batas waktu keterlambatan adalah 15 menit dari jadwal slot.</p>
                </div>
            </div>

            <!-- Footer Card -->
            <div class="bg-surface border-t border-line p-3.5 text-center text-[10px] text-slate-400">
                Dicetak secara otomatis oleh Sistem PuskesmasFlow
            </div>
        </div>
    </div>
</body>
</html>
