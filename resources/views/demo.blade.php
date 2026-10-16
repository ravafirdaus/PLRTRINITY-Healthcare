<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akun Pengujian & Sandbox Evaluator — PuskesmasFlow</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface-subtle text-ink antialiased min-h-screen font-sans flex flex-col justify-between">

    <!-- Top Header -->
    <header class="bg-white border-b border-line px-6 py-4 shadow-xs">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-hospital bg-primary flex items-center justify-center text-white shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <span class="text-lg font-bold text-ink">Puskesmas<span class="text-primary">Flow</span></span>
                    <span class="block text-[11px] text-ink-muted">Akses Khusus Evaluator &amp; Pengujian</span>
                </div>
            </a>

            <a href="{{ route('home') }}" class="text-xs font-semibold text-ink-body hover:text-primary flex items-center gap-1.5 py-1.5 px-3 rounded-hospital border border-line bg-surface hover:bg-surface-subtle transition-colors">
                <span>&larr;</span>
                <span>Kembali ke Beranda</span>
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-10 flex-1 w-full">
        <div class="mb-8">
            <span class="inline-flex px-2.5 py-1 bg-mint text-primary-dark text-xs font-semibold rounded uppercase tracking-wider border border-primary/20">
                Lingkungan Pengujian / Demo Sandbox
            </span>
            <h1 class="text-2xl sm:text-3xl font-bold text-ink mt-2">Daftar Akun Pengujian Peran</h1>
            <p class="text-xs sm:text-sm text-ink-body mt-1 max-w-2xl">
                Halaman ini disediakan khusus bagi evaluator, penguji, dan juri untuk menguji alur kerja end-to-end sesuai hak akses masing-masing peran.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            
            <!-- Pasien Mandiri -->
            <div class="p-5 rounded-hospital bg-white border border-line shadow-xs flex flex-col justify-between">
                <div>
                    <div class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-mint text-primary-dark mb-2">Peran 0</div>
                    <h3 class="text-sm font-bold text-ink mb-2">Pasien Mandiri</h3>
                    <div class="p-3 rounded bg-surface-subtle border border-line text-xs font-mono text-ink space-y-1.5">
                        <div class="text-ink-body">HP: <strong class="text-ink">081298765431</strong></div>
                        <div class="text-ink-body">Pass: <strong class="text-ink">password</strong></div>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-line">
                    <a href="{{ route('patient.login') }}" class="w-full h-8 px-3 bg-primary hover:bg-primary-hover text-white text-xs font-semibold rounded-hospital flex items-center justify-center transition-colors">
                        Login Pasien &rarr;
                    </a>
                </div>
            </div>

            <!-- Administrator -->
            <div class="p-5 rounded-hospital bg-white border border-line shadow-xs flex flex-col justify-between">
                <div>
                    <div class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 mb-2">Peran 1</div>
                    <h3 class="text-sm font-bold text-ink mb-2">Administrator</h3>
                    <div class="p-3 rounded bg-surface-subtle border border-line text-xs font-mono text-ink space-y-1.5">
                        <div class="text-ink-body truncate">admin@puskesmasflow.local</div>
                        <div class="text-ink-body">Pass: <strong class="text-ink">admin123</strong></div>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-line">
                    <a href="{{ route('login') }}" class="w-full h-8 px-3 bg-ink hover:bg-slate-800 text-white text-xs font-semibold rounded-hospital flex items-center justify-center transition-colors">
                        Login Staf &rarr;
                    </a>
                </div>
            </div>

            <!-- Petugas Loket -->
            <div class="p-5 rounded-hospital bg-white border border-line shadow-xs flex flex-col justify-between">
                <div>
                    <div class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 mb-2">Peran 2</div>
                    <h3 class="text-sm font-bold text-ink mb-2">Petugas Loket</h3>
                    <div class="p-3 rounded bg-surface-subtle border border-line text-xs font-mono text-ink space-y-1.5">
                        <div class="text-ink-body truncate">pendaftaran@puskesmasflow.local</div>
                        <div class="text-ink-body">Pass: <strong class="text-ink">daftar123</strong></div>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-line">
                    <a href="{{ route('login') }}" class="w-full h-8 px-3 bg-white hover:bg-surface-subtle border border-line text-ink text-xs font-semibold rounded-hospital flex items-center justify-center transition-colors">
                        Login Loket &rarr;
                    </a>
                </div>
            </div>

            <!-- Petugas Triase -->
            <div class="p-5 rounded-hospital bg-white border border-line shadow-xs flex flex-col justify-between">
                <div>
                    <div class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 mb-2">Peran 3</div>
                    <h3 class="text-sm font-bold text-ink mb-2">Petugas Triase</h3>
                    <div class="p-3 rounded bg-surface-subtle border border-line text-xs font-mono text-ink space-y-1.5">
                        <div class="text-ink-body truncate">triase@puskesmasflow.local</div>
                        <div class="text-ink-body">Pass: <strong class="text-ink">triase123</strong></div>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-line">
                    <a href="{{ route('login') }}" class="w-full h-8 px-3 bg-white hover:bg-surface-subtle border border-line text-ink text-xs font-semibold rounded-hospital flex items-center justify-center transition-colors">
                        Login Triase &rarr;
                    </a>
                </div>
            </div>

            <!-- Dokter Poli -->
            <div class="p-5 rounded-hospital bg-white border border-line shadow-xs flex flex-col justify-between">
                <div>
                    <div class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-sky-50 text-sky-800 mb-2">Peran 4</div>
                    <h3 class="text-sm font-bold text-ink mb-2">Dokter Poli</h3>
                    <div class="p-3 rounded bg-surface-subtle border border-line text-xs font-mono text-ink space-y-1.5">
                        <div class="text-ink-body truncate">dokter@puskesmasflow.local</div>
                        <div class="text-ink-body">Pass: <strong class="text-ink">dokter123</strong></div>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-line">
                    <a href="{{ route('login') }}" class="w-full h-8 px-3 bg-white hover:bg-surface-subtle border border-line text-ink text-xs font-semibold rounded-hospital flex items-center justify-center transition-colors">
                        Login Dokter &rarr;
                    </a>
                </div>
            </div>

        </div>

        <div class="mt-8 p-4 rounded-hospital bg-white border border-line text-xs text-ink-muted">
            <strong class="text-ink">Catatan Pengujian:</strong> Seluruh akun di atas diinisialisasi melalui database seeder Laravel dan siap digunakan tanpa registrasi manual.
        </div>
    </main>

    <!-- Minimal Footer -->
    <footer class="bg-white border-t border-line py-4 text-center text-xs text-ink-muted">
        &copy; {{ date('Y') }} PuskesmasFlow &bull; Akses Pengujian &amp; Evaluasi Sistem
    </footer>

</body>
</html>
