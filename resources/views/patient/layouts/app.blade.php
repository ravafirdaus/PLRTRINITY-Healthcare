<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PuskesmasFlow — Portal Pasien FKTP')</title>

    <!-- Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-ink bg-surface min-h-full flex" x-data="{ sidebarOpen: false }">
    
    <!-- Mobile Sidebar Backdrop & Drawer with Tactile Animations -->
    <div x-show="sidebarOpen" class="fixed inset-0 z-50 lg:hidden" x-cloak>
        <div x-show="sidebarOpen"
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"
             @click="sidebarOpen = false"></div>

        <div x-show="sidebarOpen"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="fixed inset-y-0 left-0 w-72 max-w-[85vw] bg-white shadow-2xl flex flex-col z-50 border-r border-slate-200/90">
            <!-- Mobile Drawer Content -->
            <div class="h-full flex flex-col justify-between">
                <div class="flex-1 overflow-y-auto">
                    <!-- Brand with 3D Icon -->
                    <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-gradient-to-b from-white to-slate-50/60">
                        <a href="{{ route('patient.dashboard') }}" class="flex items-center gap-3 group active:scale-95 transition-transform">
                            <div class="relative w-10 h-10 rounded-2xl p-1 bg-gradient-to-tr from-teal-500/20 via-emerald-500/10 to-teal-400/20 border border-teal-500/30 flex items-center justify-center shadow-md shadow-teal-500/10 group-hover:scale-105 group-active:scale-90 transition-all">
                                <img src="{{ asset('images/medical-3d-cross.png') }}" alt="PuskesmasFlow 3D" class="w-7 h-7 object-contain drop-shadow-xs">
                            </div>
                            <div>
                                <span class="text-base font-extrabold text-ink tracking-tight block leading-tight">
                                    Puskesmas<span class="text-primary bg-gradient-to-r from-teal-600 to-emerald-600 bg-clip-text text-transparent">Flow</span>
                                </span>
                                <span class="text-[9px] font-bold text-teal-800 uppercase tracking-wider block bg-teal-50 px-1.5 py-0.5 rounded border border-teal-200/60 w-fit mt-0.5">Portal Pasien</span>
                            </div>
                        </a>
                        <button @click="sidebarOpen = false" class="p-2 rounded-xl text-slate-400 hover:text-ink hover:bg-slate-100 transition-all active:scale-75 active:rotate-90 active:bg-rose-50 active:text-rose-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="px-5 pt-4 pb-1 text-[10px] font-bold uppercase tracking-wider text-ink-muted">
                        Menu Pasien
                    </div>

                    <nav class="p-3 space-y-1.5 text-xs font-semibold">
                        <a href="{{ route('patient.dashboard') }}"
                           class="sidebar-link flex items-center justify-between px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('patient.dashboard') ? 'bg-gradient-to-r from-teal-600 to-emerald-600 text-white font-bold shadow-md shadow-teal-600/25 active:scale-95' : 'text-slate-600 hover:text-ink hover:bg-slate-50 hover:translate-x-1 active:scale-95 active:bg-teal-50' }}">
                            <div class="flex items-center gap-3">
                                <div class="sidebar-icon w-7 h-7 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('patient.dashboard') ? 'bg-white/20 text-white shadow-inner' : 'bg-surface-subtle border border-line text-slate-500 group-hover:text-teal-600 group-hover:border-teal-200' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                </div>
                                <span>Dashboard Pasien</span>
                            </div>
                            @if(request()->routeIs('patient.dashboard'))
                                <span class="w-1.5 h-4 rounded-full bg-white/80"></span>
                            @endif
                        </a>

                        <a href="{{ route('patient.reservasi.create') }}"
                           class="sidebar-link flex items-center justify-between px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('patient.reservasi.create') ? 'bg-gradient-to-r from-teal-600 to-emerald-600 text-white font-bold shadow-md shadow-teal-600/25 active:scale-95' : 'text-slate-600 hover:text-ink hover:bg-slate-50 hover:translate-x-1 active:scale-95 active:bg-teal-50' }}">
                            <div class="flex items-center gap-3">
                                <div class="sidebar-icon w-7 h-7 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('patient.reservasi.create') ? 'bg-white/20 text-white shadow-inner' : 'bg-surface-subtle border border-line text-slate-500 group-hover:text-teal-600 group-hover:border-teal-200' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <span>Ambil Tiket Reservasi</span>
                            </div>
                            @if(request()->routeIs('patient.reservasi.create'))
                                <span class="w-1.5 h-4 rounded-full bg-white/80"></span>
                            @endif
                        </a>

                        <a href="{{ route('patient.reservasi.history') }}"
                           class="sidebar-link flex items-center justify-between px-3 py-2.5 rounded-xl transition-all group {{ (request()->routeIs('patient.reservasi.history') || request()->routeIs('patient.history') || request()->routeIs('patient.reservasi.show')) ? 'bg-gradient-to-r from-teal-600 to-emerald-600 text-white font-bold shadow-md shadow-teal-600/25 active:scale-95' : 'text-slate-600 hover:text-ink hover:bg-slate-50 hover:translate-x-1 active:scale-95 active:bg-teal-50' }}">
                            <div class="flex items-center gap-3">
                                <div class="sidebar-icon w-7 h-7 rounded-lg flex items-center justify-center transition-colors {{ (request()->routeIs('patient.reservasi.history') || request()->routeIs('patient.history')) ? 'bg-white/20 text-white shadow-inner' : 'bg-surface-subtle border border-line text-slate-500 group-hover:text-teal-600 group-hover:border-teal-200' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <span>Riwayat Tiket &amp; Kunjungan</span>
                            </div>
                            @if(request()->routeIs('patient.reservasi.history') || request()->routeIs('patient.history') || request()->routeIs('patient.reservasi.show'))
                                <span class="w-1.5 h-4 rounded-full bg-white/80"></span>
                            @endif
                        </a>

                        <a href="{{ route('patient.telekonsultasi.index') }}"
                           class="sidebar-link flex items-center justify-between px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('patient.telekonsultasi.*') ? 'bg-gradient-to-r from-teal-600 to-emerald-600 text-white font-bold shadow-md shadow-teal-600/25 active:scale-95' : 'text-slate-600 hover:text-ink hover:bg-slate-50 hover:translate-x-1 active:scale-95 active:bg-teal-50' }}">
                            <div class="flex items-center gap-3">
                                <div class="sidebar-icon w-7 h-7 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('patient.telekonsultasi.*') ? 'bg-white/20 text-white shadow-inner' : 'bg-surface-subtle border border-line text-slate-500 group-hover:text-teal-600 group-hover:border-teal-200' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </div>
                                <span>Telekonsultasi Video</span>
                            </div>
                            @if(request()->routeIs('patient.telekonsultasi.*'))
                                <span class="w-1.5 h-4 rounded-full bg-white/80"></span>
                            @endif
                        </a>

                        <a href="{{ route('patient.profile') }}"
                           class="sidebar-link flex items-center justify-between px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('patient.profile*') ? 'bg-gradient-to-r from-teal-600 to-emerald-600 text-white font-bold shadow-md shadow-teal-600/25 active:scale-95' : 'text-slate-600 hover:text-ink hover:bg-slate-50 hover:translate-x-1 active:scale-95 active:bg-teal-50' }}">
                            <div class="flex items-center gap-3">
                                <div class="sidebar-icon w-7 h-7 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('patient.profile*') ? 'bg-white/20 text-white shadow-inner' : 'bg-surface-subtle border border-line text-slate-500 group-hover:text-teal-600 group-hover:border-teal-200' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <span>Data Rekam Medis (Profil)</span>
                            </div>
                            @if(request()->routeIs('patient.profile*'))
                                <span class="w-1.5 h-4 rounded-full bg-white/80"></span>
                            @endif
                        </a>
                    </nav>
                </div>

                <div class="p-3.5 border-t border-line bg-gradient-to-b from-white to-slate-50">
                    <div class="flex items-center gap-2.5 mb-3 px-1">
                        <div class="w-8 h-8 rounded-hospital bg-teal-500/10 border border-teal-500/20 flex items-center justify-center text-teal-700 font-extrabold text-xs shrink-0 shadow-2xs">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-ink truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-teal-700 font-semibold">Pasien Terdaftar</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('patient.logout') }}">
                        @csrf
                        <button type="submit" class="btn-tap w-full h-10 flex items-center justify-center gap-2 px-3 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200/80 rounded-xl transition-all hover:shadow-xs active:scale-95 active:bg-rose-200 active:shadow-inner cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span>Keluar dari Akun</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Desktop Sidebar with 3D Depth & Polished Navigation -->
    <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:shrink-0 bg-white border-r border-slate-200/80 min-h-screen sticky top-0 h-screen z-30 shadow-xs">
        <div class="h-full flex flex-col justify-between">
            <div class="flex-1 overflow-y-auto">
                <!-- 3D Brand Logo Header -->
                <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-gradient-to-b from-white to-slate-50/50">
                    <a href="{{ route('patient.dashboard') }}" class="flex items-center gap-3 group">
                        <div class="relative w-11 h-11 rounded-2xl p-1 bg-gradient-to-tr from-teal-500/20 via-emerald-500/10 to-teal-400/20 border border-teal-500/30 flex items-center justify-center shadow-md shadow-teal-500/10 group-hover:scale-105 group-hover:shadow-teal-500/20 transition-all duration-300">
                            <img src="{{ asset('images/medical-3d-cross.png') }}" alt="PuskesmasFlow 3D" class="w-8 h-8 object-contain drop-shadow-sm group-hover:rotate-6 transition-transform">
                        </div>
                        <div>
                            <span class="text-base font-extrabold text-ink tracking-tight block leading-tight flex items-center">
                                Puskesmas<span class="text-primary bg-gradient-to-r from-teal-600 to-emerald-600 bg-clip-text text-transparent">Flow</span>
                            </span>
                            <span class="text-[9px] font-bold text-teal-800 uppercase tracking-wider block bg-teal-50 px-1.5 py-0.5 rounded border border-teal-200/60 w-fit mt-0.5">Portal Pasien</span>
                        </div>
                    </a>
                </div>

                <!-- Section Label -->
                <div class="px-5 pt-4 pb-1 text-[10px] font-bold uppercase tracking-wider text-ink-muted">
                    Layanan Pasien
                </div>

                <!-- 3D Layered Nav Links -->
                <nav class="p-3 space-y-1.5 text-xs font-semibold">
                    <a href="{{ route('patient.dashboard') }}"
                       class="sidebar-link flex items-center justify-between px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('patient.dashboard') ? 'bg-gradient-to-r from-teal-600 to-emerald-600 text-white font-bold shadow-md shadow-teal-600/25 active:scale-95' : 'text-slate-600 hover:text-ink hover:bg-slate-50 hover:translate-x-1 active:scale-95 active:bg-teal-50' }}">
                        <div class="flex items-center gap-3">
                            <div class="sidebar-icon w-7 h-7 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('patient.dashboard') ? 'bg-white/20 text-white shadow-inner' : 'bg-surface-subtle border border-line text-slate-500 group-hover:text-teal-600 group-hover:border-teal-200' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            </div>
                            <span>Dashboard Pasien</span>
                        </div>
                        @if(request()->routeIs('patient.dashboard'))
                            <span class="w-1.5 h-4 rounded-full bg-white/80"></span>
                        @endif
                    </a>

                    <a href="{{ route('patient.reservasi.create') }}"
                       class="sidebar-link flex items-center justify-between px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('patient.reservasi.create') ? 'bg-gradient-to-r from-teal-600 to-emerald-600 text-white font-bold shadow-md shadow-teal-600/25 active:scale-95' : 'text-slate-600 hover:text-ink hover:bg-slate-50 hover:translate-x-1 active:scale-95 active:bg-teal-50' }}">
                        <div class="flex items-center gap-3">
                            <div class="sidebar-icon w-7 h-7 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('patient.reservasi.create') ? 'bg-white/20 text-white shadow-inner' : 'bg-surface-subtle border border-line text-slate-500 group-hover:text-teal-600 group-hover:border-teal-200' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <span>Ambil Tiket Reservasi</span>
                        </div>
                        @if(request()->routeIs('patient.reservasi.create'))
                            <span class="w-1.5 h-4 rounded-full bg-white/80"></span>
                        @endif
                    </a>

                    <a href="{{ route('patient.reservasi.history') }}"
                       class="sidebar-link flex items-center justify-between px-3 py-2.5 rounded-xl transition-all group {{ (request()->routeIs('patient.reservasi.history') || request()->routeIs('patient.history') || request()->routeIs('patient.reservasi.show')) ? 'bg-gradient-to-r from-teal-600 to-emerald-600 text-white font-bold shadow-md shadow-teal-600/25 active:scale-95' : 'text-slate-600 hover:text-ink hover:bg-slate-50 hover:translate-x-1 active:scale-95 active:bg-teal-50' }}">
                        <div class="flex items-center gap-3">
                            <div class="sidebar-icon w-7 h-7 rounded-lg flex items-center justify-center transition-colors {{ (request()->routeIs('patient.reservasi.history') || request()->routeIs('patient.history')) ? 'bg-white/20 text-white shadow-inner' : 'bg-surface-subtle border border-line text-slate-500 group-hover:text-teal-600 group-hover:border-teal-200' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <span>Riwayat Tiket &amp; Kunjungan</span>
                        </div>
                        @if(request()->routeIs('patient.reservasi.history') || request()->routeIs('patient.history') || request()->routeIs('patient.reservasi.show'))
                            <span class="w-1.5 h-4 rounded-full bg-white/80"></span>
                        @endif
                    </a>

                    <a href="{{ route('patient.telekonsultasi.index') }}"
                       class="sidebar-link flex items-center justify-between px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('patient.telekonsultasi.*') ? 'bg-gradient-to-r from-teal-600 to-emerald-600 text-white font-bold shadow-md shadow-teal-600/25 active:scale-95' : 'text-slate-600 hover:text-ink hover:bg-slate-50 hover:translate-x-1 active:scale-95 active:bg-teal-50' }}">
                        <div class="flex items-center gap-3">
                            <div class="sidebar-icon w-7 h-7 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('patient.telekonsultasi.*') ? 'bg-white/20 text-white shadow-inner' : 'bg-surface-subtle border border-line text-slate-500 group-hover:text-teal-600 group-hover:border-teal-200' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </div>
                            <span>Telekonsultasi Video</span>
                        </div>
                        @if(request()->routeIs('patient.telekonsultasi.*'))
                            <span class="w-1.5 h-4 rounded-full bg-white/80"></span>
                        @endif
                    </a>

                    <a href="{{ route('patient.profile') }}"
                       class="sidebar-link flex items-center justify-between px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('patient.profile*') ? 'bg-gradient-to-r from-teal-600 to-emerald-600 text-white font-bold shadow-md shadow-teal-600/25 active:scale-95' : 'text-slate-600 hover:text-ink hover:bg-slate-50 hover:translate-x-1 active:scale-95 active:bg-teal-50' }}">
                        <div class="flex items-center gap-3">
                            <div class="sidebar-icon w-7 h-7 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('patient.profile*') ? 'bg-white/20 text-white shadow-inner' : 'bg-surface-subtle border border-line text-slate-500 group-hover:text-teal-600 group-hover:border-teal-200' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <span>Data Rekam Medis (Profil)</span>
                        </div>
                        @if(request()->routeIs('patient.profile*'))
                            <span class="w-1.5 h-4 rounded-full bg-white/80"></span>
                        @endif
                    </a>
                </nav>

                <!-- Mini 3D Info Badge -->
                <div class="mx-3 my-3 p-3 rounded-2xl bg-gradient-to-br from-teal-500/10 via-emerald-500/5 to-cyan-500/10 border border-teal-500/20 shadow-xs relative overflow-hidden group">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-white shadow-xs border border-teal-200/60 flex items-center justify-center text-teal-600 shrink-0 group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-ink leading-tight">Layanan Pasien FKTP</p>
                            <p class="text-[10px] text-teal-700 font-medium">Buka Hari Kerja 08:00–15:00</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom User Profile Card -->
            <div class="p-3.5 border-t border-slate-100 bg-gradient-to-b from-white to-slate-50">
                <div class="flex items-center gap-2.5 mb-3 px-1">
                    <div class="w-8 h-8 rounded-hospital bg-teal-500/10 border border-teal-500/20 flex items-center justify-center text-teal-700 font-extrabold text-xs shrink-0 shadow-2xs">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-ink truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-teal-700 font-semibold">Pasien Terdaftar</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('patient.logout') }}">
                    @csrf
                    <button type="submit" class="btn-tap w-full h-10 flex items-center justify-center gap-2 px-3 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200/80 rounded-xl transition-all hover:shadow-xs active:scale-95 active:bg-rose-200 active:shadow-inner cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Keluar dari Akun</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Column -->
    <div class="flex-1 flex flex-col min-w-0 min-h-screen">
        <!-- Top Navigation Bar with Glassmorphic Header & 3D Accent -->
        <header class="bg-white/95 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-20 px-4 sm:px-8 py-3 shadow-xs">
            <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="btn-tap lg:hidden p-2 rounded-xl text-ink-muted hover:text-ink hover:bg-surface-subtle border border-line transition-all active:scale-80 active:rotate-12 active:bg-teal-50 active:text-teal-700 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[10px] font-bold text-teal-700 uppercase tracking-wider block leading-none">Selamat Datang</span>
                        </div>
                        <span class="text-sm font-bold text-ink tracking-tight">{{ auth()->user()->name }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-2.5">
                    <a href="{{ route('public.queue') }}" target="_blank"
                       class="btn-tap inline-flex items-center gap-1.5 px-3 py-2 rounded-hospital bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 border border-slate-200 shadow-2xs hover:shadow-xs active:scale-95 transition-all">
                        <svg class="w-3.5 h-3.5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <span class="hidden sm:inline">Monitor Antrean</span>
                    </a>
                    <a href="{{ route('patient.reservasi.create') }}"
                       class="btn-tap inline-flex items-center gap-1.5 px-3.5 py-2 rounded-hospital bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white text-xs font-bold shadow-sm shadow-teal-600/20 hover:shadow-md hover:shadow-teal-600/30 active:scale-95 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span>Reservasi Baru</span>
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content Body -->
        <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6">
            <div class="max-w-7xl mx-auto">
                <!-- Alerts -->
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

                @yield('content')
            </div>
        </main>

        <footer class="border-t border-line py-4 px-6 text-center text-xs text-ink-muted">
            <p>&copy; {{ date('Y') }} PuskesmasFlow — Layanan Mandiri &amp; Sistem Antrean Pasien Faskes.</p>
        </footer>
    </div>
</body>
</html>

