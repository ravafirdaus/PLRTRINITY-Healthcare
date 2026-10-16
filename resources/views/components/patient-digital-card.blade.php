@props([
    'patient',
    'showActions' => true,
])

<div x-data="{
        copied: false,
        downloadingPdf: false,
        copyKey() {
            navigator.clipboard.writeText('{{ $patient->unique_patient_key }}');
            this.copied = true;
            setTimeout(() => this.copied = false, 2500);
        },
        async downloadCardPdf() {
            this.downloadingPdf = true;
            const element = document.getElementById('digital-card-{{ $patient->id }}');

            const loadScript = (url) => {
                return new Promise((resolve, reject) => {
                    const existing = document.querySelector(`script[src='${url}']`);
                    if (existing) {
                        resolve();
                        return;
                    }
                    const s = document.createElement('script');
                    s.src = url;
                    s.onload = resolve;
                    s.onerror = reject;
                    document.head.appendChild(s);
                });
            };

            try {
                if (typeof html2canvas === 'undefined') {
                    await loadScript('https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js');
                }
                if (typeof window.jspdf === 'undefined') {
                    await loadScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js');
                }

                // Render card to retina canvas
                const canvas = await html2canvas(element, {
                    scale: 3,
                    useCORS: true,
                    logging: false,
                    backgroundColor: '{{ $patient->is_subsidized ? '#064e3b' : '#0c2e59' }}'
                });

                const imgData = canvas.toDataURL('image/png');
                const { jsPDF } = window.jspdf;

                // Standard CR80 physical card dimensions: 85.6 mm x 54 mm (landscape)
                const pdf = new jsPDF({
                    orientation: 'landscape',
                    unit: 'mm',
                    format: [85.6, 54]
                });

                pdf.addImage(imgData, 'PNG', 0, 0, 85.6, 54, '', 'FAST');
                pdf.save('Kartu-Pasien-{{ $patient->member_number }}.pdf');
            } catch (err) {
                console.error('Error generating card PDF:', err);
                window.open('{{ route('patient.card.print') }}', '_blank');
            } finally {
                this.downloadingPdf = false;
            }
        }
     }"
     class="inline-block w-full max-w-[400px]">

    <!-- Print Isolation CSS -->
    <style>
        @media print {
            body {
                background: white !important;
            }
            .no-print, nav, header, aside, footer, .sidebar {
                display: none !important;
            }
            #digital-card-{{ $patient->id }} {
                box-shadow: none !important;
                margin: 20mm auto !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>

    <!-- Standard Physical Card Shape: 428px x 270px (CR80 ratio 85.6mm x 54mm = 1.585) -->
    <!-- Green theme for Subsidi Penuh, Blue theme for Mandiri / Reguler -->
    <div id="digital-card-{{ $patient->id }}"
         style="width: 428px; height: 270px; max-width: 100%; box-sizing: border-box;"
         class="rounded-2xl {{ $patient->is_subsidized ? 'bg-gradient-to-br from-[#064e3b] via-[#047857] to-[#065f46] border-emerald-300/40' : 'bg-gradient-to-br from-[#0c2e59] via-[#0284c7] to-[#0369a1] border-sky-300/40' }} text-white p-4 sm:px-5 sm:py-4 shadow-xl border select-none relative overflow-hidden flex flex-col justify-between transition-colors duration-300">
        
        <!-- WATERMARK GRAFIS TANDA PLUS (+) RUMAH SAKIT -->
        <!-- 1. Central Hospital Cross Watermark (Subtle & Elegant) -->
        <div class="absolute -right-4 -bottom-4 pointer-events-none opacity-[0.08] text-white z-0">
            <svg class="w-48 h-48" viewBox="0 0 100 100" fill="currentColor">
                <rect x="36" y="8" width="28" height="84" rx="8" />
                <rect x="8" y="36" width="84" height="28" rx="8" />
            </svg>
        </div>

        <!-- 2. Scattered Floating Plus Crosses -->
        <div class="absolute inset-0 pointer-events-none opacity-[0.05] flex justify-between p-3 z-0">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M10 4a1 1 0 011-1h2a1 1 0 011 1v5h5a1 1 0 011 1v2a1 1 0 01-1 1h-5v5a1 1 0 01-1 1h-2a1 1 0 01-1-1v-5H5a1 1 0 01-1-1v-2a1 1 0 011-1h5V4z"/></svg>
            <svg class="w-8 h-8 mr-16 mt-6" viewBox="0 0 24 24" fill="currentColor"><path d="M10 4a1 1 0 011-1h2a1 1 0 011 1v5h5a1 1 0 011 1v2a1 1 0 01-1 1h-5v5a1 1 0 01-1 1h-2a1 1 0 01-1-1v-5H5a1 1 0 01-1-1v-2a1 1 0 011-1h5V4z"/></svg>
        </div>

        <!-- 3. Glossy Light Reflection -->
        <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/5 to-white/10 pointer-events-none z-0"></div>

        <!-- ROW 1: HEADER (Logo, Faskes Name, e-KIP Badge) -->
        <div class="relative z-10 flex items-center justify-between border-b border-white/20 pb-2">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-white/20 backdrop-blur-md flex items-center justify-center text-white shrink-0 border border-white/30 shadow-2xs">
                    <svg class="w-5 h-5 {{ $patient->is_subsidized ? 'text-emerald-300' : 'text-sky-300' }}" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M10 4a1 1 0 011-1h2a1 1 0 011 1v5h5a1 1 0 011 1v2a1 1 0 01-1 1h-5v5a1 1 0 01-1 1h-2a1 1 0 01-1-1v-5H5a1 1 0 01-1-1v-2a1 1 0 011-1h5V4z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xs font-black uppercase tracking-tight text-white leading-tight">Puskesmas Kota Depok</h3>
                    <p class="text-[9px] {{ $patient->is_subsidized ? 'text-emerald-100' : 'text-sky-100' }} font-medium leading-none mt-0.5">Dinas Kesehatan Kota Depok</p>
                </div>
            </div>
            <div class="text-right shrink-0">
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-white/20 border border-white/30 text-white shadow-2xs">
                    e-KIP DIGITAL
                </span>
            </div>
        </div>

        <!-- ROW 2: SMART CHIP & KUNCI UNIK PASIEN -->
        <div class="relative z-10 flex items-center justify-between py-1">
            <!-- Gold EMV Chip & Contactless Waves -->
            <div class="flex items-center gap-2">
                <div class="w-8 h-6 rounded bg-gradient-to-tr from-amber-300 via-amber-200 to-amber-400 p-0.5 shadow-2xs border border-amber-500/40 flex items-center justify-center">
                    <div class="w-full h-full border border-amber-600/30 rounded flex items-center justify-around px-0.5">
                        <div class="w-1.5 h-3 border-r border-amber-600/40"></div>
                        <div class="w-1.5 h-3 border-l border-amber-600/40"></div>
                    </div>
                </div>
                <svg class="w-4 h-4 {{ $patient->is_subsidized ? 'text-emerald-200/90' : 'text-sky-200/90' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.393 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                </svg>
            </div>

            <!-- Kunci Unik Pasien -->
            <div class="text-right bg-black/25 backdrop-blur-xs px-2.5 py-1 rounded-md border border-white/20 shadow-2xs">
                <span class="text-[8px] uppercase tracking-wider {{ $patient->is_subsidized ? 'text-emerald-200' : 'text-sky-200' }} block font-semibold leading-tight">Kunci Unik Pasien</span>
                <span class="text-[11px] font-black font-mono tracking-wider {{ $patient->is_subsidized ? 'text-emerald-300' : 'text-sky-300' }} leading-tight mt-0.5 block">{{ $patient->unique_patient_key }}</span>
            </div>
        </div>

        <!-- ROW 3: NOMOR PESERTA -->
        <div class="relative z-10 py-0.5">
            <span class="text-[8px] font-semibold tracking-widest uppercase {{ $patient->is_subsidized ? 'text-emerald-200' : 'text-sky-200' }} block leading-tight">Nomor Peserta</span>
            <div class="text-lg font-black font-mono tracking-widest text-white leading-normal mt-0.5">
                {{ substr($patient->member_number, 0, 4) }} {{ substr($patient->member_number, 4, 4) }} {{ substr($patient->member_number, 8) }}
            </div>
        </div>

        <!-- ROW 4: IDENTITAS PASIEN & STATUS GOLONGAN -->
        <div class="relative z-10 border-t border-white/20 pt-2 space-y-1">
            <div class="flex justify-between items-center gap-2">
                <div class="min-w-0 flex-1">
                    <span class="font-extrabold text-white text-xs block truncate uppercase leading-normal tracking-wide">{{ $patient->name }}</span>
                </div>
                <div class="shrink-0 text-right">
                    @if($patient->is_subsidized)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-black bg-emerald-400 text-emerald-950 border border-emerald-300 shadow-2xs">
                            Subsidi Penuh (Bebas Biaya Rp 0)
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-sky-200 text-sky-950 border border-sky-300 shadow-2xs">
                            Mandiri / Reguler
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex justify-between items-center text-[9px] {{ $patient->is_subsidized ? 'text-emerald-100' : 'text-sky-100' }} font-mono leading-normal">
                <span>NIK: {{ $patient->masked_nik }}</span>
                <span>Lahir: {{ $patient->date_of_birth ? $patient->date_of_birth->translatedFormat('d/m/Y') : '-' }}</span>
            </div>

            <div class="text-[9px] {{ $patient->is_subsidized ? 'text-emerald-100/90' : 'text-sky-100/90' }} truncate leading-normal">
                Alamat: {{ $patient->address ?? 'Kota Depok, Jawa Barat' }}
            </div>
        </div>

    </div>

    <!-- ACTIONS BELOW CARD -->
    @if($showActions)
        <div class="no-print mt-3 flex flex-wrap items-center justify-between gap-1 text-xs">
            <!-- Salin Kunci Unik -->
            <button type="button" @click="copyKey()"
                    class="btn-tap inline-flex items-center gap-1 px-2.5 py-1.5 rounded-hospital border border-line bg-white hover:bg-slate-50 active:scale-95 text-slate-700 font-semibold text-[11px] shadow-xs transition">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                <span x-text="copied ? 'Tersalin!' : 'Salin Kunci'">Salin Kunci</span>
            </button>

            <div class="flex items-center gap-1.5">
                <!-- Download PDF Button -->
                <button type="button" @click="downloadCardPdf()" :disabled="downloadingPdf"
                        class="btn-tap inline-flex items-center gap-1 px-3 py-1.5 rounded-hospital bg-emerald-600 hover:bg-emerald-700 active:scale-95 disabled:opacity-60 text-white font-semibold text-[11px] shadow-xs transition cursor-pointer"
                        title="Download kartu langsung dalam format PDF 1 halaman berukuran standar">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span x-text="downloadingPdf ? 'Membuat PDF...' : 'Download PDF'">Download PDF</span>
                </button>

                <!-- Cetak Kartu -->
                <a href="{{ route('patient.card.print') }}" target="_blank"
                   class="btn-tap inline-flex items-center gap-1 px-2.5 py-1.5 rounded-hospital bg-primary hover:bg-primary-hover active:scale-95 text-white font-semibold text-[11px] shadow-xs transition"
                   title="Buka halaman cetak khusus kartu tanpa menu dasbor">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak</span>
                </a>
            </div>
        </div>
    @endif
</div>
