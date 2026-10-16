<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Kartu Pasien — {{ $patient->name }} — Puskesmas Kota Depok</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .print-card-wrapper {
                padding: 0 !important;
                margin: 0 auto !important;
                box-shadow: none !important;
            }
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            @page {
                size: A4 portrait;
                margin: 20mm auto;
            }
        }
    </style>
</head>
<body class="bg-slate-100 text-ink min-h-screen py-8 font-sans flex flex-col items-center justify-center selection:bg-primary selection:text-white">

    <!-- Top Action Bar (hidden on print) -->
    <div class="no-print max-w-md w-full mb-5 px-3 flex items-center justify-between">
        <a href="{{ route('patient.dashboard') }}" class="text-xs font-semibold text-slate-600 hover:text-ink flex items-center gap-1 transition">
            &larr; Dashboard Pasien
        </a>
        <div class="flex items-center gap-2">
            <button id="btn-dl-pdf" onclick="downloadPdf()" class="px-3.5 py-1.5 rounded-hospital bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span id="btn-text">Download PDF</span>
            </button>
            <button onclick="window.print()" class="px-3.5 py-1.5 rounded-hospital bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Kartu</span>
            </button>
        </div>
    </div>

    <!-- Print Paper Wrapper -->
    <div class="print-card-wrapper max-w-md w-full px-3 flex flex-col items-center">
        
        <!-- Standard Physical Card Shape: 428px x 270px (CR80 ratio 85.6mm x 54mm = 1.585) -->
        <!-- Green theme for Subsidi Penuh, Blue theme for Mandiri / Reguler -->
        <div id="capture-card"
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

        <p class="no-print text-center text-[11px] text-slate-500 mt-4 leading-relaxed">
            Format ukuran kartu disesuaikan dengan standar kartu fisik ISO/IEC 7810 ID-1 (85,6 mm &times; 54 mm).
        </p>
    </div>

    <script>
        async function downloadPdf() {
            const btn = document.getElementById('btn-dl-pdf');
            const btnText = document.getElementById('btn-text');
            btn.disabled = true;
            btnText.textContent = 'Membuat PDF...';

            try {
                const element = document.getElementById('capture-card');
                
                // Render crisp canvas
                const canvas = await html2canvas(element, {
                    scale: 3,
                    useCORS: true,
                    logging: false,
                    backgroundColor: '{{ $patient->is_subsidized ? '#064e3b' : '#0c2e59' }}'
                });

                const imgData = canvas.toDataURL('image/png');
                const { jsPDF } = window.jspdf;

                // Exact physical card dimensions (85.6mm x 54mm) on a single landscape page
                const pdf = new jsPDF({
                    orientation: 'landscape',
                    unit: 'mm',
                    format: [85.6, 54]
                });

                // Exactly 1 page: image fills 85.6mm x 54mm with 0 margins
                pdf.addImage(imgData, 'PNG', 0, 0, 85.6, 54, '', 'FAST');
                pdf.save('Kartu-Pasien-{{ $patient->member_number }}.pdf');
            } catch (err) {
                console.error('Gagal membuat PDF:', err);
                alert('Gagal mendownload PDF. Silakan gunakan tombol Cetak lalu pilih Simpan sebagai PDF.');
            } finally {
                btn.disabled = false;
                btnText.textContent = 'Download PDF';
            }
        }
    </script>
</body>
</html>
