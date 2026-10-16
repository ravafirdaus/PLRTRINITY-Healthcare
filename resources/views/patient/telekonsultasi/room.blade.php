@extends('patient.layouts.app')

@section('title', 'Ruang Telekonsultasi Video — PuskesmasFlow')

@section('content')
<div class="space-y-4"
     x-data="patientVideoRoom({
        domain: '{{ $jitsiConfig['domain'] }}',
        roomName: '{{ $jitsiConfig['roomName'] }}',
        displayName: '{{ $jitsiConfig['displayName'] }}',
        subject: '{{ $jitsiConfig['subject'] ?? '' }}',
        isDemo: {{ $jitsiConfig['isDemo'] ? 'true' : 'false' }},
        exitUrl: '{{ route('patient.telekonsultasi.show', $teleconsultation) }}'
     })"
     x-init="initJitsi()"
     x-on:beforeunload.window="cleanup()">

    <!-- Demo Mode Warning Banner (Requirement #1 & #6) -->
    @if($jitsiConfig['isDemo'])
        <div class="bg-amber-500/10 border border-amber-500/30 text-amber-900 rounded-hospital px-3.5 py-2 text-xs mb-3 flex flex-wrap sm:flex-nowrap items-center justify-between gap-2.5">
            <div class="flex items-center gap-2">
                <span class="px-1.5 py-0.5 rounded bg-amber-600 text-white font-black text-[10px] tracking-wider uppercase shrink-0">Mode Demo</span>
                <span class="font-semibold text-amber-950">Server demonstrasi publik Jitsi ({{ $jitsiConfig['domain'] }})</span>
            </div>
            <span class="text-[11px] text-amber-800 bg-amber-100 border border-amber-300 px-2.5 py-0.5 rounded font-medium">
                Sesi konsultasi akan terbuka penuh setelah dokter membuka ruangan.
            </span>
        </div>
    @endif

    <!-- Video Header Bar -->
    <div class="bg-white rounded-hospital border border-line px-4 sm:px-6 py-3.5 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-hospital bg-primary-50 text-primary flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="font-bold text-ink text-sm sm:text-base leading-tight">{{ $teleconsultation->service->name }}</h1>
                    <!-- Connection Status Badge -->
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold"
                          :class="{
                              'bg-amber-50 text-amber-700 border border-amber-200': connectionState === 'connecting',
                              'bg-emerald-50 text-accent border border-emerald-200': connectionState === 'connected',
                              'bg-rose-50 text-rose-700 border border-rose-200': connectionState === 'disconnected' || connectionState === 'error'
                          }">
                        <span class="w-1.5 h-1.5 rounded-full"
                              :class="{
                                  'bg-amber-500 animate-pulse': connectionState === 'connecting',
                                  'bg-emerald-500': connectionState === 'connected',
                                  'bg-rose-500': connectionState === 'disconnected' || connectionState === 'error'
                              }"></span>
                        <span x-text="connectionLabel"></span>
                    </span>
                </div>
                <div class="text-xs text-slate-500 mt-0.5 flex flex-wrap items-center gap-2">
                    <span>Dokter: <strong class="text-ink font-semibold">{{ $teleconsultation->doctor?->name ?? 'Dokter Pemeriksa' }}</strong></span>
                    <span class="text-slate-300">&bull;</span>
                    <span>Durasi: <span class="font-mono font-semibold text-primary" x-text="callDurationFormatted">00:00</span></span>
                </div>
            </div>
        </div>

        <!-- Quick Video Header Controls -->
        <div class="flex items-center gap-2 self-end sm:self-center">
            <!-- Fullscreen Toggle -->
            <button type="button" @click="toggleFullscreen()"
                    class="p-2.5 rounded-hospital border border-line text-xs font-semibold bg-surface hover:bg-slate-100 text-slate-700 transition"
                    title="Layar Penuh Video">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
            </button>

            <!-- End Call Button -->
            <button type="button" @click="confirmEndCall = true"
                    class="px-3.5 py-2 rounded-hospital bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M5 3a2 2 0 00-2 2v1c0 8.284 6.716 15 15 15h1a2 2 0 002-2v-3.28a1 1 0 00-.684-.948l-4.493-1.498a1 1 0 00-1.21.502l-1.13 2.257a11.042 11.042 0 01-5.516-5.517l2.257-1.128a1 1 0 00.502-1.21L9.228 3.683A1 1 0 008.279 3H5z"/></svg>
                <span class="hidden sm:inline">Keluar Panggilan</span>
            </button>
        </div>
    </div>

    <!-- Main Video Frame Container -->
    <div id="patient-video-wrapper" class="patient-video-frame relative bg-slate-950 rounded-hospital overflow-hidden border border-slate-800 shadow-md w-full">
        <!-- Loading State -->
        <div x-show="loading" class="absolute inset-0 flex flex-col items-center justify-center text-white bg-slate-950 z-10 space-y-3 p-4 text-center">
            <span class="inline-block w-8 h-8 border-3 border-primary border-t-transparent rounded-full animate-spin"></span>
            <p class="text-xs font-semibold text-slate-300">Memuat modul konferensi video PuskesmasFlow...</p>
        </div>

        <!-- Error State -->
        <div x-show="errorMessage" x-cloak class="absolute inset-0 flex flex-col items-center justify-center text-white bg-slate-950 z-20 p-6 text-center space-y-3">
            <div class="w-12 h-12 rounded-full bg-rose-500/20 text-rose-400 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="font-bold text-sm text-rose-300">Gagal Menghubungkan ke Layanan Video</h3>
            <p class="text-xs text-slate-400 max-w-md" x-text="errorMessage"></p>
            <div class="pt-2 flex items-center gap-3">
                <button type="button" @click="retryJitsi()" class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-hospital text-xs font-semibold transition">
                    Coba Hubungkan Ulang
                </button>
                <a :href="exitUrl" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-hospital text-xs font-semibold transition">
                    Kembali ke Detail
                </a>
            </div>
        </div>

        <!-- Jitsi Iframe Target Container -->
        <div id="jitsi-meet-container" class="w-full h-full"></div>
    </div>

    <!-- Confirm End Call Modal -->
    <div x-show="confirmEndCall" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-hospital p-6 max-w-sm w-full border border-line shadow-xl space-y-4" @click.away="confirmEndCall = false">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-ink text-sm">Keluar dari Ruang Konsultasi?</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Anda dapat bergabung kembali selama sesi belum diselesaikan oleh dokter.</p>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" @click="confirmEndCall = false"
                        class="px-3.5 py-2 rounded-hospital border border-line text-xs font-semibold text-slate-700 hover:bg-surface">
                    Lanjutkan Konsultasi
                </button>
                <button type="button" @click="hangUpAndExit()"
                        class="px-4 py-2 rounded-hospital bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs">
                    Keluar Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Jitsi Iframe API Script Loader -->
<script src="https://{{ $jitsiConfig['domain'] }}/external_api.js"></script>

<style>
    .patient-video-frame {
        height: 65vh;
        height: calc(100vh - 180px);
        height: calc(100dvh - 180px);
        min-height: 440px;
        max-height: 840px;
    }

    @media (max-width: 1023px) {
        .patient-video-frame {
            height: 55vh;
            min-height: 380px;
            max-height: 620px;
        }
    }

    #jitsi-meet-container,
    #jitsi-meet-container iframe {
        width: 100% !important;
        height: 100% !important;
        min-width: 100% !important;
        border: none !important;
        display: block !important;
    }
</style>

<script>
function patientVideoRoom(config) {
    return {
        domain: config.domain,
        roomName: config.roomName,
        displayName: config.displayName,
        subject: config.subject || '',
        isDemo: config.isDemo,
        exitUrl: config.exitUrl,
        jitsiApi: null,
        loading: true,
        connectionState: 'connecting', // 'connecting', 'connected', 'disconnected', 'error'
        connectionLabel: 'Menghubungkan...',
        audioMuted: false,
        videoMuted: false,
        confirmEndCall: false,
        callDurationSeconds: 0,
        timerInterval: null,
        isFullscreen: false,

        get callDurationFormatted() {
            const m = Math.floor(this.callDurationSeconds / 60).toString().padStart(2, '0');
            const s = (this.callDurationSeconds % 60).toString().padStart(2, '0');
            return `${m}:${s}`;
        },

        initJitsi() {
            this.loading = true;
            this.errorMessage = null;

            if (typeof JitsiMeetExternalAPI === 'undefined') {
                this.loading = false;
                this.connectionState = 'error';
                this.connectionLabel = 'Layanan Video Tidak Tersedia';
                this.errorMessage = 'Tidak dapat memuat pustaka Jitsi Meet External API dari server ' + this.domain + '. Periksa koneksi internet Anda atau firewall jaringan.';
                return;
            }

            try {
                const container = document.getElementById('jitsi-meet-container');
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
                        DEFAULT_REMOTE_DISPLAY_NAME: 'Dokter Pemeriksa'
                    }
                };

                this.jitsiApi = new JitsiMeetExternalAPI(this.domain, options);

                // Event Listeners
                this.jitsiApi.addEventListener('videoConferenceJoined', (event) => {
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
                    window.location.href = this.exitUrl;
                });

                this.jitsiApi.addEventListener('audioMuteStatusChanged', (e) => {
                    this.audioMuted = !!e.muted;
                });

                this.jitsiApi.addEventListener('videoMuteStatusChanged', (e) => {
                    this.videoMuted = !!e.muted;
                });

                this.jitsiApi.addEventListener('cameraError', (e) => {
                    console.warn('Camera error event:', e);
                });

                this.jitsiApi.addEventListener('micError', (e) => {
                    console.warn('Mic error event:', e);
                });

                // Safety timeout: if after 15 seconds still connecting, show helpful message
                setTimeout(() => {
                    if (this.connectionState === 'connecting') {
                        this.loading = false;
                    }
                }, 15000);

            } catch (err) {
                console.error('Failed to initialize Jitsi:', err);
                this.loading = false;
                this.connectionState = 'error';
                this.connectionLabel = 'Gagal';
                this.errorMessage = 'Terjadi kesalahan saat memulai ruang video: ' + (err.message || 'Kesalahan tak dikenal');
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
            const wrapper = document.getElementById('patient-video-wrapper');
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
                }, 1000);
            }
        },

        hangUpAndExit() {
            this.cleanup();
            window.location.href = this.exitUrl;
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
                    // Ignore dispose errors on page unload
                }
                this.jitsiApi = null;
            }
        }
    };
}
</script>
@endsection
