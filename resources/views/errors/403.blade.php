<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 — Akses Ditolak | PuskesmasFlow</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-ink bg-surface min-h-full flex items-center justify-center p-4 sm:p-6">
    <div class="max-w-lg w-full bg-white rounded-hospital border border-line p-6 sm:p-8 shadow-md text-center space-y-5">
        <!-- Brand Logo -->
        <div class="inline-flex items-center gap-2.5">
            <div class="w-10 h-10 rounded-hospital bg-primary/10 border border-primary/20 flex items-center justify-center text-primary shadow-xs">
                <svg class="w-5 h-5 text-primary" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 4a1 1 0 011-1h2a1 1 0 011 1v5h5a1 1 0 011 1v2a1 1 0 01-1 1h-5v5a1 1 0 01-1 1h-2a1 1 0 01-1-1v-5H5a1 1 0 01-1-1v-2a1 1 0 011-1h5V4z"/>
                </svg>
            </div>
            <span class="text-lg font-bold text-ink tracking-tight">
                Puskesmas<span class="text-primary">Flow</span>
            </span>
        </div>

        <!-- 403 Badge & Icon -->
        <div class="mx-auto w-16 h-16 rounded-full bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-600">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>

        <div>
            <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 text-[11px] font-bold tracking-wider uppercase">Error 403 &bull; Akses Ditolak</span>
            <h1 class="text-lg sm:text-xl font-extrabold text-ink mt-2 leading-tight">
                Anda Tidak Memiliki Izin Akses
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                {{ $exception->getMessage() ?: 'Anda tidak memiliki hak akses atau izin peran yang sesuai untuk membuka halaman atau melakukan tindakan ini.' }}
            </p>
        </div>

        @auth
            <!-- Current Active Session Context Box -->
            <div class="bg-surface-subtle border border-line rounded-hospital p-3.5 text-left text-xs space-y-1.5">
                <div class="flex items-center justify-between text-slate-500 pb-1.5 border-b border-line">
                    <span class="font-bold text-[10px] uppercase tracking-wider">Sesi Akun Saat Ini:</span>
                    <span class="font-mono text-[10px] px-1.5 py-0.5 rounded bg-white border border-line">{{ ucfirst(auth()->user()->role) }}</span>
                </div>
                <div class="flex items-center gap-2 pt-0.5">
                    <div class="w-7 h-7 rounded-full bg-primary/10 text-primary font-bold text-xs flex items-center justify-center shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-bold text-ink truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-slate-500 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>

                @if(auth()->user()->role === 'patient')
                    <div class="mt-2 pt-2 border-t border-line text-[11px] text-amber-800 bg-amber-50/80 p-2.5 rounded border border-amber-200/60 leading-normal">
                        💡 <strong>Catatan Pengujian:</strong> Jika Anda sedang menguji telekonsultasi dokter dan pasien di perangkat yang sama, gunakan <strong>Jendela Penyamaran (Incognito / Private Window)</strong> untuk akun Pasien agar sesi akun Dokter tidak tertimpa.
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center gap-2 pt-2">
                <a href="{{ route('dashboard') }}"
                   class="w-full sm:flex-1 py-2.5 px-4 rounded-hospital bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-xs transition text-center">
                    Ke Dashboard Saya
                </a>
                <form method="POST" action="{{ route('logout') }}" class="w-full sm:flex-1">
                    @csrf
                    <button type="submit"
                            class="w-full py-2.5 px-4 rounded-hospital bg-white hover:bg-slate-50 border border-line text-slate-700 text-xs font-semibold shadow-xs transition">
                        Keluar &amp; Ganti Akun
                    </button>
                </form>
            </div>
        @else
            <!-- Guest Action -->
            <div class="pt-2">
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center py-2.5 px-5 rounded-hospital bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-xs transition">
                    Masuk ke Sistem (Login)
                </a>
            </div>
        @endauth

        <div class="pt-2 border-t border-line">
            <span class="text-[10px] text-slate-400">PuskesmasFlow &bull; Sistem Informasi &amp; Antrean Faskes Terpadu</span>
        </div>
    </div>
</body>
</html>
