<x-app-layout :fluid="true" :compact="true">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
            <div class="flex items-center gap-2.5 min-w-0">
                <a href="{{ route('doctor.telekonsultasi.show', $teleconsultation) }}"
                   title="Kembali ke Detail Telekonsultasi"
                   class="p-1.5 rounded-hospital border border-line text-slate-500 hover:text-ink hover:bg-surface shrink-0 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>
                        <h1 class="text-sm sm:text-base font-bold text-ink leading-tight truncate">
                            Telekonsultasi: {{ $teleconsultation->patient->name }}
                        </h1>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border shrink-0 {{ $teleconsultation->status_badge_class }}">
                            {{ $teleconsultation->status_label }}
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-500 truncate mt-0.5">
                        {{ $teleconsultation->service->name }} &bull; NIK: {{ $teleconsultation->patient->masked_nik }} &bull; {{ $teleconsultation->patient->age }} th
                    </div>
                </div>
            </div>

            <!-- Header Status & Live Duration -->
            <div class="flex items-center gap-2 shrink-0 self-start sm:self-auto">
                <div class="bg-surface px-2.5 py-1 rounded-hospital border border-line text-xs font-semibold flex items-center gap-1.5 shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-slate-400 text-[11px]">Durasi:</span>
                    <span id="call-duration-timer" class="font-mono text-primary font-bold text-xs">00:00</span>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Demo Mode Notice & Host Instruction -->
    @if($jitsiConfig['isDemo'])
        <div class="bg-amber-500/10 border border-amber-500/20 text-amber-900 rounded-hospital px-3 py-1.5 text-xs mb-3 flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2 min-w-0">
                <span class="px-1.5 py-0.5 rounded bg-amber-600 text-white font-bold text-[10px] tracking-wider uppercase shrink-0">Mode Demo</span>
                <span class="font-semibold text-amber-950 truncate">Server publik Jitsi ({{ $jitsiConfig['domain'] }})</span>
            </div>
            <span class="text-[11px] text-amber-800 bg-amber-100/80 border border-amber-300/70 px-2 py-0.5 rounded font-medium">
                💡 Jika muncul <em>"Waiting for host"</em>, klik <strong>Log-in</strong> di frame video.
            </span>
        </div>
    @endif

    <!-- Main Workspace Grid: Responsive stacked on tablet/small screen, 2-column on xl+ -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-3.5 2xl:gap-4 items-start"
         x-data="doctorVideoRoom({
            domain: '{{ $jitsiConfig['domain'] }}',
            roomName: '{{ $jitsiConfig['roomName'] }}',
            displayName: '{{ $jitsiConfig['displayName'] }}',
            subject: '{{ $jitsiConfig['subject'] ?? '' }}',
            isDemo: {{ $jitsiConfig['isDemo'] ? 'true' : 'false' }},
            exitUrl: '{{ route('doctor.telekonsultasi.show', $teleconsultation) }}'
         })"
         x-init="initJitsi()"
         x-on:beforeunload.window="cleanup()">

        <!-- Video Conference Container: Flexible width & dynamic viewport-based height -->
        <div class="flex flex-col space-y-2 min-w-0 transition-all duration-200"
             :class="wideMode ? 'col-span-12' : 'xl:col-span-8 2xl:col-span-8'">
            <div id="doctor-video-wrapper"
                 class="doctor-video-frame relative bg-slate-950 rounded-hospital overflow-hidden border border-slate-800 shadow-md w-full">
                <!-- Loading State -->
                <div x-show="loading" class="absolute inset-0 flex flex-col items-center justify-center text-white bg-slate-950 z-10 space-y-3 p-4 text-center">
                    <span class="inline-block w-8 h-8 border-3 border-primary border-t-transparent rounded-full animate-spin"></span>
                    <p class="text-xs font-semibold text-slate-300">Menghubungkan ke ruang telekonsultasi dokter...</p>
                </div>

                <!-- Error State -->
                <div x-show="errorMessage" x-cloak class="absolute inset-0 flex flex-col items-center justify-center text-white bg-slate-950 z-20 p-6 text-center space-y-3">
                    <div class="w-12 h-12 rounded-full bg-rose-500/20 text-rose-400 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 class="font-bold text-sm text-rose-300">Gagal Menghubungkan Ruang Video</h3>
                    <p class="text-xs text-slate-400 max-w-md" x-text="errorMessage"></p>
                    <button type="button" @click="retryJitsi()" class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-hospital text-xs font-semibold transition">
                        Coba Lagi
                    </button>
                </div>

                <!-- Jitsi Target Container -->
                <div id="doctor-jitsi-container" class="w-full h-full"></div>
            </div>

            <!-- Media Bar Controls: Compact and Accessible -->
            <div class="bg-white rounded-hospital border border-line px-3 py-2 shadow-2xs flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-1.5 flex-wrap">

                    <!-- Wide Mode Toggle -->
                    <button type="button" @click="toggleWideMode()"
                            class="px-2.5 py-1.5 rounded-hospital border border-line text-xs font-semibold bg-surface hover:bg-slate-100 text-slate-700 transition flex items-center gap-1.5"
                            :title="wideMode ? 'Kembali ke Tampilan Standar' : 'Perluas Video (Tampilan Penuh)'">
                        <template x-if="!wideMode">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                        </template>
                        <template x-if="wideMode">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0h4m-4 0v4m11 5l5 5m0 0v-4m0 4h-4"/></svg>
                        </template>
                        <span x-text="wideMode ? 'Tampilan Standar' : 'Perluas Video'"></span>
                    </button>

                    <!-- Fullscreen Toggle -->
                    <button type="button" @click="toggleFullscreen()"
                            class="p-1.5 rounded-hospital border border-line text-xs font-semibold bg-surface hover:bg-slate-100 text-slate-700 transition flex items-center justify-center shrink-0"
                            title="Layar Penuh Video">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                    </button>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" @click="retryJitsi()"
                            class="p-1.5 rounded border border-line text-slate-500 hover:text-ink hover:bg-surface text-xs shrink-0"
                            title="Muat Ulang Video">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </button>
                    <div class="text-[11px] text-slate-500 font-medium">
                        Status: <span class="font-bold text-ink" x-text="connectionLabel">Menghubungkan</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Columns: Medical Workspace & Clinical Notes Form -->
        <div class="flex flex-col space-y-3 min-w-0 transition-all duration-200"
             :class="wideMode ? 'col-span-12 grid grid-cols-1 md:grid-cols-2 gap-4 space-y-0' : 'xl:col-span-4 2xl:col-span-4'">
            <!-- Patient Info Accordion / Box -->
            <div class="bg-white rounded-hospital border border-line p-3.5 shadow-2xs text-xs space-y-2.5">
                <div class="flex items-center justify-between pb-2 border-b border-line">
                    <span class="font-bold text-ink uppercase tracking-wider text-[11px]">Identitas Pasien</span>
                    <span class="font-mono text-slate-500 text-[11px]">{{ $teleconsultation->patient->masked_nik }}</span>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <span class="text-slate-400 block text-[10px]">Nama:</span>
                        <span class="font-bold text-ink">{{ $teleconsultation->patient->name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px]">JK / Usia:</span>
                        <span class="font-semibold text-ink">{{ $teleconsultation->patient->gender_label }}, {{ $teleconsultation->patient->age }} th</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px]">No. Telepon / WA:</span>
                        <span class="font-mono text-ink">{{ $teleconsultation->patient->phone ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px]">No. BPJS:</span>
                        <span class="font-mono text-ink">{{ $teleconsultation->patient->bpjs_number ?? '-' }}</span>
                    </div>
                </div>

                @if($teleconsultation->appointment?->notes)
                    <div class="pt-2 border-t border-line">
                        <span class="text-slate-400 block text-[10px] font-bold uppercase">Keluhan Awal Pasien:</span>
                        <p class="text-slate-700 bg-surface p-2 rounded border border-line mt-1 text-xs">{{ $teleconsultation->appointment->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Complete Consultation Form (Save Clinical Notes & Disposition) -->
            <div class="bg-white rounded-hospital border border-line p-4 shadow-2xs flex-1 flex flex-col justify-between space-y-3">
                <form method="POST" action="{{ route('doctor.telekonsultasi.complete', $teleconsultation) }}" id="complete-consultation-form" class="space-y-3 flex-1 flex flex-col">
                    @csrf

                    <div>
                        <h3 class="font-bold text-ink text-xs sm:text-sm flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Pencatatan Klinis & Tindak Lanjut</span>
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Catat hasil konsultasi langsung selagi memeriksa pasien.</p>
                    </div>

                    <div class="flex-1 flex flex-col space-y-1">
                        <label for="clinical_notes" class="block text-[11px] font-bold text-slate-700 uppercase">
                            Catatan Medis & Anamnesis <span class="text-slate-400 font-normal lowercase">(opsional)</span>
                        </label>
                        <textarea name="clinical_notes" id="clinical_notes" rows="4"
                                  class="w-full rounded-hospital border-line text-xs focus:ring-1 focus:ring-primary focus:border-primary p-2.5"
                                  placeholder="Tuliskan temuan anamnesis, instruksi terapi, atau edukasi perawatan pasien...">{{ old('clinical_notes', $teleconsultation->clinical_notes) }}</textarea>
                        @error('clinical_notes')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="disposition" class="block text-[11px] font-bold text-slate-700 uppercase mb-1">
                            Disposisi / Tindak Lanjut <span class="text-red-500">*</span>
                        </label>
                        <select name="disposition" id="disposition" required
                                class="w-full rounded-hospital border-line text-xs font-semibold text-ink focus:ring-1 focus:ring-primary focus:border-primary">
                            <option value="pulang" {{ old('disposition', $teleconsultation->disposition) === 'pulang' ? 'selected' : '' }}>Selesai / Pulang Rawat Jalan</option>
                            <option value="resep_obat" {{ old('disposition', $teleconsultation->disposition) === 'resep_obat' ? 'selected' : '' }}>Ambil Resep Obat di Farmasi Puskesmas</option>
                            <option value="rujuk_lanjut" {{ old('disposition', $teleconsultation->disposition) === 'rujuk_lanjut' ? 'selected' : '' }}>Rujuk Lanjutan (Faskes Tingkat 2 / RSUD)</option>
                            <option value="kontrol_ulang" {{ old('disposition', $teleconsultation->disposition) === 'kontrol_ulang' ? 'selected' : '' }}>Jadwalkan Kontrol Ulang</option>
                        </select>
                        @error('disposition')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            onclick="return confirm('Apakah Anda yakin ingin menyelesaikan sesi telekonsultasi ini dan menyimpan catatan medis?')"
                            class="w-full py-2.5 px-3 rounded-hospital bg-primary hover:bg-primary-hover text-white font-bold text-xs shadow-xs transition flex items-center justify-center gap-1.5 mt-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Selesaikan & Simpan Konsultasi</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<!-- Jitsi Iframe API Script Loader -->
<script src="https://{{ $jitsiConfig['domain'] }}/external_api.js"></script>

<style>
    /* Responsive Video Frame Height with Safe Viewport Bounds */
    .doctor-video-frame {
        height: 60vh;
        height: calc(100vh - 175px);
        height: calc(100dvh - 175px);
        min-height: 440px;
        max-height: 820px;
    }

    @media (max-width: 1023px) {
        .doctor-video-frame {
            height: 52vh;
            min-height: 380px;
            max-height: 600px;
        }
    }

    #doctor-jitsi-container,
    #doctor-jitsi-container iframe {
        width: 100% !important;
        height: 100% !important;
        min-width: 100% !important;
        border: none !important;
        display: block !important;
    }
</style>

<script>
function doctorVideoRoom(config) {
    return {
        domain: config.domain,
        roomName: config.roomName,
        displayName: config.displayName,
        subject: config.subject || '',
        isDemo: config.isDemo,
        exitUrl: config.exitUrl,
        jitsiApi: null,
        loading: true,
        errorMessage: null,
        connectionState: 'connecting',
        connectionLabel: 'Menghubungkan...',
        audioMuted: false,
        videoMuted: false,
        callDurationSeconds: 0,
        timerInterval: null,
        isFullscreen: false,
        wideMode: false,

        toggleWideMode() {
            this.wideMode = !this.wideMode;
        },

        initJitsi() {
            this.loading = true;
            this.errorMessage = null;

            if (typeof JitsiMeetExternalAPI === 'undefined') {
                this.loading = false;
                this.connectionState = 'error';
                this.connectionLabel = 'Gagal';
                this.errorMessage = 'Tidak dapat memuat modul Jitsi Meet External API dari ' + this.domain + '. Periksa koneksi jaringan server.';
                return;
            }

            try {
                const container = document.getElementById('doctor-jitsi-container');
                container.innerHTML = '';

                const options = {
                    roomName: this.roomName,
                    parentNode: container,
                    width: '100%',
                    height: '100%',
                    userInfo: {
                        displayName: this.displayName
                    },
                    configOverwrite: {
                        prejoinConfig: {
                            enabled: false
                        },
                        prejoinPageEnabled: false,
                        startWithAudioMuted: false,
                        startWithVideoMuted: false,
                        disableDeepLinking: true,
                        enableWelcomePage: false,
                        enableClosePage: false,
                        readOnlyName: true,
                        toolbarButtons: [
                            'microphone', 'camera', 'closedcaptions', 'desktop', 'fullscreen',
                            'fodeviceselection', 'hangup', 'chat', 'raisehand',
                            'videoquality', 'filmstrip', 'tileview'
                        ]
                    },
                    interfaceConfigOverwrite: {
                        TOOLBAR_BUTTONS: [
                            'microphone', 'camera', 'closedcaptions', 'desktop', 'fullscreen',
                            'fodeviceselection', 'hangup', 'chat', 'raisehand',
                            'videoquality', 'filmstrip', 'tileview'
                        ],
                        SHOW_JITSI_WATERMARK: false,
                        SHOW_WATERMARK_FOR_GUESTS: false,
                        DEFAULT_REMOTE_DISPLAY_NAME: 'Pasien'
                    }
                };

                this.jitsiApi = new JitsiMeetExternalAPI(this.domain, options);

                this.jitsiApi.addEventListener('videoConferenceJoined', () => {
                    this.loading = false;
                    this.connectionState = 'connected';
                    this.connectionLabel = 'Tersambung';
                    this.startDurationTimer();

                    if (this.subject) {
                        try {
                            this.jitsiApi.executeCommand('subject', this.subject);
                            this.jitsiApi.executeCommand('localSubject', this.subject);
                        } catch (e) {
                            // ignore
                        }
                    }
                });

                this.jitsiApi.addEventListener('videoConferenceLeft', () => {
                    this.connectionState = 'disconnected';
                    this.connectionLabel = 'Panggilan Berakhir';
                    this.cleanup();
                });

                this.jitsiApi.addEventListener('audioMuteStatusChanged', (e) => {
                    this.audioMuted = !!e.muted;
                });

                this.jitsiApi.addEventListener('videoMuteStatusChanged', (e) => {
                    this.videoMuted = !!e.muted;
                });

                setTimeout(() => {
                    if (this.connectionState === 'connecting') {
                        this.loading = false;
                    }
                }, 15000);

            } catch (err) {
                console.error('Failed to init doctor Jitsi:', err);
                this.loading = false;
                this.connectionState = 'error';
                this.connectionLabel = 'Gagal';
                this.errorMessage = 'Kesalahan menghubungkan Jitsi: ' + (err.message || 'Error');
            }
        },

        toggleAudio() {
            if (this.jitsiApi) {
                this.jitsiApi.executeCommand('toggleAudio');
            }
        },

        toggleVideo() {
            if (this.jitsiApi) {
                this.jitsiApi.executeCommand('toggleVideo');
            }
        },

        toggleFullscreen() {
            const wrapper = document.getElementById('doctor-video-wrapper');
            if (!wrapper) return;

            if (!document.fullscreenElement) {
                if (wrapper.requestFullscreen) {
                    wrapper.requestFullscreen();
                } else if (wrapper.webkitRequestFullscreen) {
                    wrapper.webkitRequestFullscreen();
                }
                this.isFullscreen = true;
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                }
                this.isFullscreen = false;
            }
        },

        startDurationTimer() {
            if (!this.timerInterval) {
                this.timerInterval = setInterval(() => {
                    this.callDurationSeconds++;
                    const m = Math.floor(this.callDurationSeconds / 60).toString().padStart(2, '0');
                    const s = (this.callDurationSeconds % 60).toString().padStart(2, '0');
                    const el = document.getElementById('call-duration-timer');
                    if (el) el.textContent = `${m}:${s}`;
                }, 1000);
            }
        },

        retryJitsi() {
            this.cleanup();
            this.initJitsi();
        },

        cleanup() {
            if (this.timerInterval) {
                clearInterval(this.timerInterval);
                this.timerInterval = null;
            }
            if (this.jitsiApi) {
                try {
                    this.jitsiApi.dispose();
                } catch (e) {
                    // ignore
                }
                this.jitsiApi = null;
            }
        }
    };
}
</script>
