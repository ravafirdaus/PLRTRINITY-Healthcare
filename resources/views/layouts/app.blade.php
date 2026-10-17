<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PuskesmasFlow') }} — Portal Petugas Medis</title>

        <!-- Fonts: Plus Jakarta Sans -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-ink bg-surface-subtle min-h-full flex selection:bg-primary selection:text-white" x-data="{ sidebarOpen: false, desktopSidebarCollapsed: false }">
        
        <!-- Mobile Sidebar Backdrop & Off-canvas -->
        <div x-show="sidebarOpen" class="fixed inset-0 z-50 lg:hidden" x-cloak>
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity" @click="sidebarOpen = false"></div>
            <div class="fixed inset-y-0 left-0 w-64 bg-white shadow-xl flex flex-col z-50 border-r border-line">
                @include('layouts.navigation', ['isMobile' => true])
            </div>
        </div>

        <!-- Desktop Static Sidebar -->
        <aside class="hidden lg:flex lg:flex-col bg-white border-r border-line min-h-screen sticky top-0 h-screen z-30 shadow-2xs transition-all duration-200"
               :class="desktopSidebarCollapsed ? 'w-16' : 'w-56 xl:w-64 lg:shrink-0'">
            @include('layouts.navigation', ['isMobile' => false])
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 min-h-screen">
            
            <!-- Top Header Bar -->
            <header class="bg-white/95 backdrop-blur-md border-b border-line sticky top-0 z-20 px-3 sm:px-6 {{ ($compact ?? false) ? 'py-2 sm:py-2.5' : 'py-3 sm:py-3.5' }} shadow-2xs">
                <div class="{{ ($fluid ?? false) ? 'w-full max-w-full' : 'max-w-7xl mx-auto' }} flex items-center justify-between gap-4">
                    
                    <!-- Left: Mobile Toggle, Desktop Collapse & Page Title / Breadcrumb -->
                    <div class="flex items-center gap-2.5">
                        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-1.5 rounded-hospital text-ink-muted hover:text-ink hover:bg-surface-subtle border border-line transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        <button @click="desktopSidebarCollapsed = !desktopSidebarCollapsed"
                                class="hidden lg:flex p-1.5 rounded-hospital text-ink-muted hover:text-ink hover:bg-surface-subtle border border-line transition-colors"
                                :title="desktopSidebarCollapsed ? 'Buka Sidebar' : 'Ciutkan Sidebar'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                            </svg>
                        </button>

                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse hidden sm:inline-block"></span>
                            <div>
                                <span class="text-xs font-semibold text-accent uppercase tracking-wider block leading-none">PuskesmasFlow Medis</span>
                                <span class="text-sm font-bold text-ink tracking-tight">Standar Pelayanan FKTP</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Quick Utilities & User Status -->
                    <div class="flex items-center gap-3">
                        <!-- Quick View Monitor Antrean -->
                        <a href="{{ route('public.queue') }}" target="_blank"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-hospital bg-surface-subtle hover:bg-slate-100 text-xs font-semibold text-ink border border-line transition-colors"
                           title="Buka Layar Monitor Antrean Publik">
                            <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <span class="hidden sm:inline">Layar Monitor</span>
                        </a>

                        <div class="h-6 w-px bg-line hidden sm:block"></div>

                        <!-- Current User Role Badge -->
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-hospital bg-primary/10 border border-primary/20 flex items-center justify-center text-primary font-bold text-xs shrink-0">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                            <div class="hidden md:block text-left">
                                <span class="text-xs font-bold text-ink block leading-tight truncate max-w-[140px]">{{ Auth::user()->name }}</span>
                                <span class="text-[10px] font-semibold text-primary block leading-tight">
                                    {{ match(Auth::user()->role) {
                                        'admin' => 'Administrator',
                                        'registrar' => 'Petugas Loket',
                                        'health_officer' => 'Perawat Triase',
                                        'doctor' => 'Dokter Poli',
                                        default => ucfirst(Auth::user()->role),
                                    } }}
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            </header>

            <!-- Page Header Area (if passed) -->
            @isset($header)
                <div class="bg-surface-subtle border-b border-line {{ ($compact ?? false) ? 'px-3 sm:px-6 py-2' : 'px-4 sm:px-8 py-3.5' }}">
                    <div class="{{ ($fluid ?? false) ? 'w-full max-w-full' : 'max-w-7xl mx-auto' }}">
                        {{ $header }}
                    </div>
                </div>
            @endisset

            <!-- Page Main Content Body -->
            <main class="flex-1 {{ ($compact ?? false) ? 'px-3 sm:px-4 lg:px-6 py-2.5 sm:py-3.5' : 'px-4 sm:px-6 lg:px-8 py-6' }}">
                <div class="{{ ($fluid ?? false) ? 'w-full max-w-full' : 'max-w-7xl mx-auto' }}">
                    
                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div x-data="{ show: true }" x-show="show" class="flex items-center justify-between p-4 mb-6 text-emerald-900 bg-emerald-50 rounded-hospital border border-emerald-200 shadow-2xs transition-all">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-hospital bg-emerald-100 flex items-center justify-center text-emerald-700 shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-xs sm:text-sm font-semibold">{{ session('success') }}</span>
                            </div>
                            <button @click="show = false" class="text-emerald-600 hover:text-emerald-800 p-1 rounded-hospital hover:bg-emerald-100 transition-colors">&times;</button>
                        </div>
                    @endif

                    @if(session('error') || $errors->any())
                        <div x-data="{ show: true }" x-show="show" class="p-4 mb-6 text-rose-900 bg-rose-50 rounded-hospital border border-rose-200 shadow-2xs transition-all">
                            <div class="flex items-center justify-between mb-1">
                                <div class="flex items-center gap-2 font-bold text-xs sm:text-sm text-rose-800">
                                    <div class="w-6 h-6 rounded-hospital bg-rose-100 flex items-center justify-center text-rose-700 shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    </div>
                                    <span>Pemberitahuan Sistem:</span>
                                </div>
                                <button @click="show = false" class="text-rose-600 hover:text-rose-800 p-1 rounded-hospital hover:bg-rose-100 transition-colors font-bold">&times;</button>
                            </div>
                            @if(session('error'))
                                <p class="text-xs sm:text-sm ms-8 text-rose-700 font-medium">{{ session('error') }}</p>
                            @endif
                            @if($errors->any())
                                <ul class="list-disc ms-12 text-xs space-y-1 mt-1 text-rose-700">
                                    @foreach($errors->all() as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </main>

            <!-- Bottom Footnote -->
            <footer class="border-t border-line {{ ($compact ?? false) ? 'py-2 px-4 text-[11px]' : 'py-4 px-6 text-xs' }} text-center text-ink-muted">
                <p>&copy; {{ date('Y') }} PuskesmasFlow — Sistem Manajemen Pelayanan &amp; Antrean Faskes Terpadu.</p>
            </footer>

        </div>
    </body>
</html>
