<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-surface">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PuskesmasFlow') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-ink bg-surface antialiased min-h-screen flex flex-col justify-between">
        <!-- Top Institutional Accent Bar -->
        <div class="h-1 bg-gradient-to-r from-primary to-accent"></div>

        <div class="flex-1 flex flex-col justify-center items-center py-12 px-4 sm:px-6">
            <div class="mb-6 text-center">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3 group">
                    <div class="w-12 h-12 rounded-hospital bg-primary flex items-center justify-center text-white shadow-sm shadow-primary/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <div class="text-left">
                        <div class="text-xl font-extrabold text-ink tracking-tight">
                            <span>Puskesmas<span class="text-primary">Flow</span></span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium">Sistem Informasi Layanan Faskes</p>
                    </div>
                </a>
            </div>

            <div class="w-full sm:max-w-md bg-white border border-line rounded-hospital shadow-sm p-6 sm:p-8">
                {{ $slot }}
            </div>
        </div>

        <!-- Minimal Institutional Footer -->
        <footer class="py-4 border-t border-line text-center text-xs text-slate-400 bg-white">
            &copy; {{ date('Y') }} PuskesmasFlow &bull; Sistem Informasi &amp; Pelayanan Kesehatan Terpadu
        </footer>
    </body>
</html>
