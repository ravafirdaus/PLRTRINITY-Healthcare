<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cek Status Reservasi — PuskesmasFlow</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface text-ink antialiased selection:bg-primary selection:text-white font-sans min-h-screen flex flex-col justify-between">
    <!-- Top Institutional Accent Bar -->
    <div class="h-1 bg-gradient-to-r from-primary to-accent"></div>

    <header class="bg-white border-b border-line">
        <div class="max-w-4xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="SIPUSKES Medical Center" class="w-9 h-9 object-contain drop-shadow-xs shrink-0">
                <div class="text-left">
                    <span class="text-base font-extrabold text-ink tracking-tight">Puskesmas<span class="text-primary">Flow</span></span>
                </div>
            </a>
            <a href="{{ route('public.appointments.create') }}" class="text-xs font-semibold text-primary hover:text-primary-hover">
                + Buat Reservasi Baru
            </a>
        </div>
    </header>

    <main class="py-12 flex-1">
        <div class="max-w-2xl mx-auto px-4">
            <div class="text-center mb-8">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight">Cek Status Reservasi Pasien</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Masukkan Nomor Induk Kependudukan (NIK) atau Nomor Reservasi Anda</p>
            </div>

            <!-- Search Form -->
            <form action="{{ route('public.appointments.search') }}" method="GET" class="mb-8">
                <div class="flex gap-2">
                    <input type="text" name="q" value="{{ $query }}" required placeholder="Contoh: 320101... atau RES-2026..."
                           class="flex-grow rounded-hospital border-line focus:border-primary focus:ring-1 focus:ring-primary text-sm font-mono p-3">
                    <button type="submit" class="px-5 py-3 bg-primary hover:bg-primary-hover text-white font-semibold text-xs sm:text-sm rounded-hospital transition-colors shadow-xs">
                        Cari Tiket
                    </button>
                </div>
            </form>

            @if($query)
                <div class="space-y-4">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Hasil Pencarian ({{ count($appointments) }})</h2>

                    @if($appointments->isEmpty())
                        <div class="bg-white rounded-hospital p-8 text-center border border-line text-slate-500 text-sm">
                            Tidak ditemukan reservasi dengan kata kunci: <strong class="text-ink">{{ $query }}</strong>.
                        </div>
                    @else
                        @foreach($appointments as $appt)
                            <div class="bg-white rounded-hospital p-5 border border-line shadow-xs flex items-center justify-between hover:border-primary/40 transition-colors">
                                <div>
                                    <div class="font-mono font-bold text-primary text-base">{{ $appt->appointment_number }}</div>
                                    <div class="text-xs font-bold text-ink mt-0.5">{{ $appt->patient->name }} &bull; {{ $appt->service->name }}</div>
                                    <div class="text-[11px] text-slate-500 mt-1">
                                        {{ $appt->appointment_date->translatedFormat('d M Y') }} &bull; Slot: {{ substr($appt->slot_time, 0, 5) }} WIB
                                    </div>
                                </div>

                                <div class="text-right">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                                        {{ match($appt->status) {
                                            'booked' => 'bg-amber-50 text-amber-700 border border-amber-200',
                                            'checked_in' => 'bg-emerald-50 text-accent border border-emerald-200',
                                            'completed' => 'bg-sky-50 text-primary border border-sky-200',
                                            default => 'bg-surface text-slate-800 border border-line'
                                        } }}">
                                        {{ ucfirst($appt->status) }}
                                    </span>
                                    <div class="mt-2">
                                        <a href="{{ route('public.appointments.show', $appt->appointment_number) }}" class="text-xs font-semibold text-primary hover:text-primary-hover">
                                            Lihat Bukti Tiket &rarr;
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            @endif
        </div>
    </main>

    <!-- Institutional Footer -->
    <footer class="py-4 border-t border-line text-center text-xs text-slate-400 bg-white">
        &copy; {{ date('Y') }} PuskesmasFlow &bull; Sistem Layanan Faskes Terpadu
    </footer>
</body>
</html>
