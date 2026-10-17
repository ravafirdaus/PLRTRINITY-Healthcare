<div class="h-full flex flex-col justify-between overflow-x-hidden">
    <!-- Top: Logo & Navigation Links -->
    <div class="flex-1 overflow-y-auto">
        <!-- Logo Brand -->
        <div class="p-4 border-b border-line flex items-center justify-between"
             :class="desktopSidebarCollapsed ? 'lg:p-3 lg:justify-center' : 'p-5'">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group" title="PuskesmasFlow">
                <img src="{{ asset('images/logo.png') }}" alt="SIPUSKES Medical Center" class="w-9 h-9 object-contain drop-shadow-xs group-hover:scale-105 transition-transform shrink-0">
                <div :class="desktopSidebarCollapsed ? 'lg:hidden' : ''" class="transition-opacity duration-150">
                    <span class="text-base font-extrabold text-ink tracking-tight block leading-tight">
                        Puskesmas<span class="text-primary font-black">Flow</span>
                    </span>
                    <span class="text-[9px] font-bold text-primary uppercase tracking-wider block bg-primary-50 px-1.5 py-0.5 rounded border border-primary/20 w-fit mt-0.5">Portal Staf Medis</span>
                </div>
            </a>
            @if(isset($isMobile) && $isMobile)
                <button @click="sidebarOpen = false" class="p-1.5 rounded-hospital text-ink-muted hover:text-ink hover:bg-surface-subtle lg:hidden">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            @endif
        </div>

        <!-- Section Label -->
        <div :class="desktopSidebarCollapsed ? 'lg:hidden' : ''" class="px-5 pt-4 pb-1 text-[10px] font-bold uppercase tracking-wider text-ink-muted">
            Menu Operasional
        </div>

        <!-- Navigation Links -->
        <nav class="p-2 lg:p-3 space-y-1 text-xs font-semibold">
            <!-- Dashboard Link -->
            <a href="{{ route('dashboard') }}"
               title="Dashboard Utama"
               class="flex items-center gap-3 px-3 py-2.5 rounded-hospital transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary text-white font-bold shadow-2xs' : 'text-ink-muted hover:text-ink hover:bg-surface-subtle' }}"
               :class="desktopSidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-ink-muted' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span :class="desktopSidebarCollapsed ? 'lg:hidden' : ''">Dashboard Utama</span>
            </a>

            @if(auth()->user()->role === 'registrar' || auth()->user()->role === 'admin')
                <!-- Loket Pendaftaran -->
                <a href="{{ route('registrar.dashboard') }}"
                   title="Loket Pendaftaran"
                   class="flex items-center justify-between px-3 py-2.5 rounded-hospital transition-colors {{ request()->routeIs('registrar.*') ? 'bg-primary text-white font-bold shadow-2xs' : 'text-ink-muted hover:text-ink hover:bg-surface-subtle' }}"
                   :class="desktopSidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('registrar.*') ? 'text-white' : 'text-ink-muted' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        <span :class="desktopSidebarCollapsed ? 'lg:hidden' : ''">Loket Pendaftaran</span>
                    </div>
                    <span :class="desktopSidebarCollapsed ? 'lg:hidden' : ''" class="text-[10px] px-1.5 py-0.5 rounded-hospital font-mono {{ request()->routeIs('registrar.*') ? 'bg-white/20 text-white' : 'bg-surface-subtle text-ink-muted border border-line' }}">Loket</span>
                </a>
            @endif

            @if(auth()->user()->role === 'health_officer' || auth()->user()->role === 'admin')
                <!-- Meja Triase -->
                <a href="{{ route('triage.index') }}"
                   title="Meja Triase Klinis"
                   class="flex items-center justify-between px-3 py-2.5 rounded-hospital transition-colors {{ request()->routeIs('triage.*') ? 'bg-primary text-white font-bold shadow-2xs' : 'text-ink-muted hover:text-ink hover:bg-surface-subtle' }}"
                   :class="desktopSidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('triage.*') ? 'text-white' : 'text-ink-muted' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span :class="desktopSidebarCollapsed ? 'lg:hidden' : ''">Meja Triase Klinis</span>
                    </div>
                    <span :class="desktopSidebarCollapsed ? 'lg:hidden' : ''" class="text-[10px] px-1.5 py-0.5 rounded-hospital font-mono {{ request()->routeIs('triage.*') ? 'bg-white/20 text-white' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">ATS</span>
                </a>
            @endif

            @if(auth()->user()->role === 'doctor' || auth()->user()->role === 'admin')
                <!-- Pelayanan Poli -->
                <a href="{{ route('doctor.dashboard') }}"
                   title="Pemeriksaan Poli"
                   class="flex items-center justify-between px-3 py-2.5 rounded-hospital transition-colors {{ (request()->routeIs('doctor.dashboard') || request()->routeIs('doctor.call') || request()->routeIs('doctor.start')) ? 'bg-primary text-white font-bold shadow-2xs' : 'text-ink-muted hover:text-ink hover:bg-surface-subtle' }}"
                   :class="desktopSidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0 {{ (request()->routeIs('doctor.dashboard') || request()->routeIs('doctor.call') || request()->routeIs('doctor.start')) ? 'text-white' : 'text-ink-muted' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span :class="desktopSidebarCollapsed ? 'lg:hidden' : ''">Pemeriksaan Poli</span>
                    </div>
                    <span :class="desktopSidebarCollapsed ? 'lg:hidden' : ''" class="text-[10px] px-1.5 py-0.5 rounded-hospital font-mono {{ (request()->routeIs('doctor.dashboard') || request()->routeIs('doctor.call') || request()->routeIs('doctor.start')) ? 'bg-white/20 text-white' : 'bg-sky-50 text-sky-700 border border-sky-200' }}">Dokter</span>
                </a>

                <!-- Telekonsultasi Poli -->
                <a href="{{ route('doctor.telekonsultasi.index') }}"
                   title="Telekonsultasi Video"
                   class="flex items-center justify-between px-3 py-2.5 rounded-hospital transition-colors {{ request()->routeIs('doctor.telekonsultasi.*') ? 'bg-primary text-white font-bold shadow-2xs' : 'text-ink-muted hover:text-ink hover:bg-surface-subtle' }}"
                   :class="desktopSidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('doctor.telekonsultasi.*') ? 'text-white' : 'text-ink-muted' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <span :class="desktopSidebarCollapsed ? 'lg:hidden' : ''">Telekonsultasi Video</span>
                    </div>
                    <span :class="desktopSidebarCollapsed ? 'lg:hidden' : ''" class="text-[10px] px-1.5 py-0.5 rounded-hospital font-mono {{ request()->routeIs('doctor.telekonsultasi.*') ? 'bg-white/20 text-white' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">Online</span>
                </a>
            @endif

            <!-- Section Label -->
            <div :class="desktopSidebarCollapsed ? 'lg:hidden' : ''" class="px-2 pt-4 pb-1 text-[10px] font-bold uppercase tracking-wider text-ink-muted">
                Audit & Tata Kelola
            </div>

            <!-- Laporan -->
            <a href="{{ route('reports.index') }}"
               title="Laporan & Audit Pelayanan"
               class="flex items-center gap-3 px-3 py-2.5 rounded-hospital transition-colors {{ request()->routeIs('reports.*') ? 'bg-primary text-white font-bold shadow-2xs' : 'text-ink-muted hover:text-ink hover:bg-surface-subtle' }}"
               :class="desktopSidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('reports.*') ? 'text-white' : 'text-ink-muted' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span :class="desktopSidebarCollapsed ? 'lg:hidden' : ''">Laporan & Audit</span>
            </a>

            @if(auth()->user()->role === 'admin')
                <!-- Pengaturan -->
                <a href="{{ route('admin.dashboard') }}"
                   title="Pengaturan Sistem"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-hospital transition-colors {{ request()->routeIs('admin.*') ? 'bg-primary text-white font-bold shadow-2xs' : 'text-ink-muted hover:text-ink hover:bg-surface-subtle' }}"
                   :class="desktopSidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.*') ? 'text-white' : 'text-ink-muted' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span :class="desktopSidebarCollapsed ? 'lg:hidden' : ''">Pengaturan Sistem</span>
                </a>
            @endif

            <!-- Profil Saya -->
            <a href="{{ route('profile.edit') }}"
               title="Pengaturan Akun"
               class="flex items-center gap-3 px-3 py-2.5 rounded-hospital transition-colors {{ request()->routeIs('profile.*') ? 'bg-primary text-white font-bold shadow-2xs' : 'text-ink-muted hover:text-ink hover:bg-surface-subtle' }}"
               :class="desktopSidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('profile.*') ? 'text-white' : 'text-ink-muted' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span :class="desktopSidebarCollapsed ? 'lg:hidden' : ''">Pengaturan Akun</span>
            </a>
        </nav>
    </div>

    <!-- Bottom: User Profile Card & Logout -->
    <div class="p-3 lg:p-4 border-t border-line bg-surface-subtle">
        <div class="flex items-center gap-2.5 mb-3 px-1" :class="desktopSidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
            <div class="w-8 h-8 rounded-hospital bg-primary/10 border border-primary/20 flex items-center justify-center text-primary font-bold text-xs shrink-0"
                 :title="'{{ Auth::user()->name }}'">
                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
            </div>
            <div :class="desktopSidebarCollapsed ? 'lg:hidden' : ''" class="min-w-0 flex-1">
                <p class="text-xs font-bold text-ink truncate">{{ Auth::user()->name }}</p>
                <p class="text-[10px] text-ink-muted truncate">
                    {{ match(Auth::user()->role) {
                        'admin' => 'Admin Sistem',
                        'registrar' => 'Petugas Pendaftaran',
                        'health_officer' => 'Petugas Triase',
                        'doctor' => 'Dokter Poli',
                        default => ucfirst(Auth::user()->role),
                    } }}
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    title="Keluar dari Sesi"
                    class="w-full h-9 flex items-center justify-center gap-2 px-2 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200/80 rounded-hospital transition-colors">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span :class="desktopSidebarCollapsed ? 'lg:hidden' : ''">Keluar</span>
            </button>
        </form>
    </div>
</div>

