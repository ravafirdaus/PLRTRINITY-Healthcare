<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tiket Antrean {{ $queueEntry->queue_code }} — PuskesmasFlow</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface text-ink antialiased selection:bg-primary selection:text-white py-10 font-sans">
    <div class="max-w-xs mx-auto px-4">
        <!-- Actions (Hide on Print) -->
        <div class="mb-5 flex items-center justify-between print:hidden">
            <a href="{{ route('registrar.dashboard') }}" class="text-xs font-semibold text-slate-500 hover:text-ink flex items-center gap-1">
                &larr; Loket Pendaftaran
            </a>
            <button onclick="window.print()" class="px-3.5 py-1.5 rounded-hospital bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-xs transition-colors">
                Cetak Tiket
            </button>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 rounded-hospital bg-emerald-50 border border-emerald-200 text-accent text-xs font-bold print:hidden">
                {{ session('success') }}
            </div>
        @endif

        <!-- Struk Termal Card -->
        <div class="bg-white rounded-hospital border border-line shadow-sm p-6 text-center space-y-4">
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-hospital bg-primary flex items-center justify-center text-white mb-2 shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <h1 class="text-base font-extrabold text-ink tracking-tight">PUSKESMASFLOW</h1>
                <p class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">Tiket Antrean Pelayanan Pasien</p>
                <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $queueEntry->created_at->translatedFormat('d/m/Y H:i') }} WIB</div>
            </div>

            <div class="py-4 border-y-2 border-dashed border-line">
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">NOMOR ANTREAN</div>
                <div class="text-4xl sm:text-5xl font-black font-mono tracking-tight text-ink my-2 select-all">
                    {{ $queueEntry->queue_code }}
                </div>
                <div class="text-sm font-extrabold text-primary">
                    {{ $queueEntry->service->name }}
                </div>
            </div>

            <div class="text-left text-xs space-y-1.5 py-1 text-slate-600">
                <div class="flex justify-between">
                    <span class="text-slate-400">Pasien:</span>
                    <span class="font-bold text-ink">{{ $queueEntry->patient->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Jalur:</span>
                    <span class="font-semibold text-ink uppercase text-[10px]">{{ $queueEntry->registration_type }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Prioritas:</span>
                    <span class="font-bold text-[10px] uppercase {{ $queueEntry->priority_level === 1 ? 'text-red-600' : 'text-ink' }}">
                        {{ $queueEntry->priority_label }}
                    </span>
                </div>
                <div class="pt-1.5 mt-1 border-t border-dashed border-line">
                    @if($queueEntry->patient->bpjs_number)
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400 text-[11px]">Golongan:</span>
                            <span class="font-extrabold text-emerald-700 text-xs">Bebas Biaya (Rp 0)</span>
                        </div>
                        <div class="text-[9px] text-slate-400 text-right">Subsidi Penuh — Tanpa Tagihan &amp; Iuran</div>
                    @else
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400 text-[11px]">Golongan:</span>
                            <span class="font-bold text-ink text-xs">Mandiri / Reguler</span>
                        </div>
                        <div class="text-[9px] text-slate-500 text-right">Estimasi Rp 25.000 • Loket / QRIS</div>
                    @endif
                </div>
            </div>

            <div class="p-3 bg-surface rounded-hospital text-[10px] text-slate-500 leading-normal border border-line">
                Silakan menunggu panggilan di Ruang Meja Triase / Poli Tujuan. Pantau nomor antrean di layar monitor ruang tunggu.
            </div>

            <div class="text-[9px] text-slate-400 font-mono">
                Terima kasih atas kunjungan Anda
            </div>
        </div>
    </div>
</body>
</html>
