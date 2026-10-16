<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pendaftaran Pasien & Penerbitan e-KIP — PuskesmasFlow</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface min-h-screen text-ink antialiased font-sans flex flex-col justify-between"
      x-data="{
          showPassword: false,
          name: '{{ old('name', '') }}',
          nik: '{{ old('nik', '') }}',
          dateOfBirth: '{{ old('date_of_birth', '') }}',
          address: '{{ old('address', '') }}',
          bpjsNumber: '{{ old('bpjs_number', '') }}',
          patientTier: '{{ old('patient_tier', 'subsidi') }}',
          get formattedNik() {
              if (!this.nik) return '3276 ••••••••••';
              if (this.nik.length <= 10) return this.nik;
              return this.nik.substring(0, 6) + '******' + this.nik.substring(this.nik.length - 4);
          },
          get formattedDob() {
              if (!this.dateOfBirth) return 'DD/MM/YYYY';
              const parts = this.dateOfBirth.split('-');
              if (parts.length === 3) return `${parts[2]}/${parts[1]}/${parts[0]}`;
              return this.dateOfBirth;
          }
      }">

    <!-- Top Accent Bar -->
    <div class="h-1 bg-primary"></div>

    <!-- MAIN REGISTRATION SECTION -->
    <div class="flex-1 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">
            
            <!-- Header Brand -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center gap-2.5 mb-3">
                    <div class="w-10 h-10 rounded-hospital bg-primary/10 border border-primary/20 flex items-center justify-center text-primary shadow-xs">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M10 4a1 1 0 011-1h2a1 1 0 011 1v5h5a1 1 0 011 1v2a1 1 0 01-1 1h-5v5a1 1 0 01-1 1h-2a1 1 0 01-1-1v-5H5a1 1 0 01-1-1v-2a1 1 0 011-1h5V4z"/>
                        </svg>
                    </div>
                    <div class="text-left">
                        <span class="text-xl font-bold tracking-tight text-ink">Puskesmas<span class="text-primary">Flow</span></span>
                        <span class="block text-[10px] font-semibold text-accent uppercase tracking-wider">Registrasi &amp; e-KIP Digital Pasien</span>
                    </div>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight">Pendaftaran Akun &amp; Kartu Pasien Digital</h1>
                <p class="mt-1.5 text-xs sm:text-sm text-ink-muted max-w-xl mx-auto leading-relaxed">
                    Daftar akun satu kali untuk menerbitkan <strong>Kartu Pasien Digital (e-KIP)</strong> dengan transparansi subsidi biaya. Saat membuat reservasi nanti, Anda tidak perlu memilih jenis biaya lagi.
                </p>
            </div>

            <!-- Two-Column Layout: Form on Left, Live e-KIP Preview on Right -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- LEFT COLUMN: REGISTRATION FORM (Cols 7) -->
                <div class="lg:col-span-7 bg-white rounded-hospital border border-line shadow-card p-6 sm:p-8">
                    <form method="POST" action="{{ route('patient.register.store') }}">
                        @csrf

                        {{-- 1. Data Identitas --}}
                        <fieldset class="mb-7">
                            <div class="flex items-center gap-2 pb-2.5 mb-4 border-b border-line">
                                <span class="w-6 h-6 rounded-hospital bg-primary/10 text-primary text-xs font-bold flex items-center justify-center">1</span>
                                <legend class="text-sm font-bold text-ink uppercase tracking-wide">Data Identitas Pasien</legend>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <label for="nik" class="block text-xs font-semibold text-ink mb-1.5">Nomor Induk Kependudukan (NIK) <span class="text-rose-600">*</span></label>
                                    <input type="text" name="nik" id="nik" x-model="nik" value="{{ old('nik') }}" 
                                        class="w-full h-10 px-3.5 rounded-hospital border border-line text-sm text-ink placeholder:text-ink-muted/50 focus:border-primary focus:ring-1 focus:ring-primary outline-hidden transition-colors @error('nik') border-rose-500 @enderror"
                                        placeholder="16 digit sesuai e-KTP" maxlength="16" inputmode="numeric" required>
                                    @error('nik')
                                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="name" class="block text-xs font-semibold text-ink mb-1.5">Nama Lengkap Pasien <span class="text-rose-600">*</span></label>
                                    <input type="text" name="name" id="name" x-model="name" value="{{ old('name') }}"
                                        class="w-full h-10 px-3.5 rounded-hospital border border-line text-sm text-ink placeholder:text-ink-muted/50 focus:border-primary focus:ring-1 focus:ring-primary outline-hidden transition-colors @error('name') border-rose-500 @enderror"
                                        placeholder="Nama sesuai kartu identitas resmi" required>
                                    @error('name')
                                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="date_of_birth" class="block text-xs font-semibold text-ink mb-1.5">Tanggal Lahir <span class="text-rose-600">*</span></label>
                                    <input type="date" name="date_of_birth" id="date_of_birth" x-model="dateOfBirth" value="{{ old('date_of_birth') }}"
                                        class="w-full h-10 px-3.5 rounded-hospital border border-line text-sm text-ink focus:border-primary focus:ring-1 focus:ring-primary outline-hidden transition-colors @error('date_of_birth') border-rose-500 @enderror"
                                        max="{{ date('Y-m-d') }}" required>
                                    @error('date_of_birth')
                                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-ink mb-1.5">Jenis Kelamin <span class="text-rose-600">*</span></label>
                                    <div class="h-10 flex items-center space-x-6 px-1">
                                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-medium text-ink">
                                            <input type="radio" name="gender" value="male" {{ old('gender', 'male') === 'male' ? 'checked' : '' }}
                                                class="w-4 h-4 text-primary focus:ring-primary border-line" required>
                                            <span>Laki-laki</span>
                                        </label>
                                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-medium text-ink">
                                            <input type="radio" name="gender" value="female" {{ old('gender') === 'female' ? 'checked' : '' }}
                                                class="w-4 h-4 text-primary focus:ring-primary border-line">
                                            <span>Perempuan</span>
                                        </label>
                                    </div>
                                    @error('gender')
                                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </fieldset>

                        {{-- 2. Kontak & Alamat --}}
                        <fieldset class="mb-7">
                            <div class="flex items-center gap-2 pb-2.5 mb-4 border-b border-line">
                                <span class="w-6 h-6 rounded-hospital bg-primary/10 text-primary text-xs font-bold flex items-center justify-center">2</span>
                                <legend class="text-sm font-bold text-ink uppercase tracking-wide">Kontak &amp; Alamat Domisili</legend>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label for="phone" class="block text-xs font-semibold text-ink mb-1.5">Nomor Handphone / WhatsApp Aktif <span class="text-rose-600">*</span></label>
                                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                                        class="w-full h-10 px-3.5 rounded-hospital border border-line text-sm text-ink placeholder:text-ink-muted/50 focus:border-primary focus:ring-1 focus:ring-primary outline-hidden transition-colors @error('phone') border-rose-500 @enderror"
                                        placeholder="Contoh: 08123456789" required>
                                    <p class="mt-1 text-[11px] text-ink-muted">Nomor ini digunakan sebagai akun login Anda dan penerima notifikasi antrean faskes.</p>
                                    @error('phone')
                                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="address" class="block text-xs font-semibold text-ink mb-1.5">Alamat Tempat Tinggal / Domisili <span class="text-ink-muted font-normal">(opsional)</span></label>
                                    <textarea name="address" id="address" x-model="address" rows="2"
                                        class="w-full p-3 rounded-hospital border border-line text-sm text-ink placeholder:text-ink-muted/50 focus:border-primary focus:ring-1 focus:ring-primary outline-hidden transition-colors @error('address') border-rose-500 @enderror"
                                        placeholder="Contoh: Perumahan Mutiara Blok A/2, RT 01/RW 05, Sawangan, Kota Depok">{{ old('address') }}</textarea>
                                    @error('address')
                                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </fieldset>

                        {{-- 3. Transparansi Subsidi & Pembayaran Berdasarkan Golongan --}}
                        <fieldset class="mb-7">
                            <div class="flex items-center gap-2 pb-2.5 mb-3 border-b border-line">
                                <span class="w-6 h-6 rounded-hospital bg-primary/10 text-primary text-xs font-bold flex items-center justify-center">3</span>
                                <legend class="text-sm font-bold text-ink uppercase tracking-wide">Transparansi Subsidi &amp; Biaya Layanan</legend>
                            </div>

                            <p class="text-xs text-ink-muted mb-3.5 leading-relaxed">
                                Pilih kategori golongan kepesertaan Anda. Pilihan ini akan <strong>tertera langsung di Kartu Pasien Digital (e-KIP)</strong> resmi Anda, sehingga saat melakukan reservasi antrean di masa mendatang, sistem otomatis menerapkan tarif sesuai hak Anda tanpa perlu memilih biaya lagi.
                            </p>

                            <div class="space-y-3">
                                <!-- Option A: Subsidi Penuh (Menengah ke Bawah) -->
                                <label class="relative block cursor-pointer">
                                    <input type="radio" name="patient_tier" value="subsidi" x-model="patientTier" class="sr-only peer">
                                    <div class="p-4 rounded-hospital border transition-all"
                                         :class="patientTier === 'subsidi' ? 'border-emerald-500 bg-emerald-50/40 ring-2 ring-emerald-500/20 shadow-xs' : 'border-line hover:border-slate-300 bg-white'">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <div class="flex items-center gap-2">
                                                <div class="w-4 h-4 rounded-full border flex items-center justify-center"
                                                     :class="patientTier === 'subsidi' ? 'border-emerald-600 bg-emerald-600' : 'border-slate-300'">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-white" x-show="patientTier === 'subsidi'"></div>
                                                </div>
                                                <span class="text-xs font-bold text-ink">Subsidi Penuh (Menengah ke Bawah)</span>
                                            </div>
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                Bebas Biaya (Rp 0)
                                            </span>
                                        </div>

                                        <p class="text-[11px] text-slate-600 leading-relaxed pl-6">
                                            Ditampilkan status <strong>"Bebas Biaya (Rp 0)"</strong> secara transparan tanpa iuran bulanan dan tanpa tagihan sama sekali (Program JKN PBI / KIS / Subsidi Kesehatan Pemda Kota Depok).
                                        </p>

                                        <!-- BPJS / KIS Input (Active if Subsidi) -->
                                        <div class="mt-3 pt-3 border-t border-emerald-200/80 pl-6" x-show="patientTier === 'subsidi'" x-transition>
                                            <label for="bpjs_number" class="block text-[11px] font-semibold text-emerald-950 mb-1">
                                                Nomor Kartu BPJS Kesehatan / KIS <span class="text-slate-400 font-normal">(opsional jika subsidi faskes)</span>
                                            </label>
                                            <input type="text" name="bpjs_number" id="bpjs_number" x-model="bpjsNumber" value="{{ old('bpjs_number') }}"
                                                class="w-full h-9 px-3 rounded-hospital border border-emerald-300 text-xs text-ink placeholder:text-slate-400 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-hidden bg-white"
                                                placeholder="Contoh: 0001234567890 (13 digit)">
                                            @error('bpjs_number')
                                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </label>

                                <!-- Option B: Mandiri / Reguler (Menengah ke Atas) -->
                                <label class="relative block cursor-pointer">
                                    <input type="radio" name="patient_tier" value="mandiri" x-model="patientTier" class="sr-only peer">
                                    <div class="p-4 rounded-hospital border transition-all"
                                         :class="patientTier === 'mandiri' ? 'border-sky-500 bg-sky-50/40 ring-2 ring-sky-500/20 shadow-xs' : 'border-line hover:border-slate-300 bg-white'">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <div class="flex items-center gap-2">
                                                <div class="w-4 h-4 rounded-full border flex items-center justify-center"
                                                     :class="patientTier === 'mandiri' ? 'border-sky-600 bg-sky-600' : 'border-slate-300'">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-white" x-show="patientTier === 'mandiri'"></div>
                                                </div>
                                                <span class="text-xs font-bold text-ink">Mandiri / Reguler (Menengah ke Atas)</span>
                                            </div>
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-sky-800 border border-sky-300">
                                                Pasien Umum
                                            </span>
                                        </div>

                                        <p class="text-[11px] text-slate-600 leading-relaxed pl-6 mb-2">
                                            Transparansi estimasi rincian biaya dengan opsi pembayaran <em>on-the-spot</em> di loket Puskesmas (Tunai/Debit) atau Digital (QRIS).
                                        </p>

                                        <!-- Estimasi Rincian Biaya Transparan -->
                                        <div class="ml-6 p-2.5 rounded bg-white border border-sky-200 text-xs space-y-1">
                                            <div class="flex justify-between text-slate-600 text-[11px]">
                                                <span>Retribusi Administrasi &amp; Pendaftaran:</span>
                                                <span class="font-mono font-semibold text-ink">Rp 10.000</span>
                                            </div>
                                            <div class="flex justify-between text-slate-600 text-[11px]">
                                                <span>Jasa Konsultasi Dokter &amp; Skrining Awal:</span>
                                                <span class="font-mono font-semibold text-ink">Rp 15.000</span>
                                            </div>
                                            <div class="flex justify-between pt-1 border-t border-slate-100 font-bold text-sky-950 text-xs">
                                                <span>Total Estimasi Biaya Kunjungan:</span>
                                                <span class="font-mono text-sm text-sky-900">Rp 25.000</span>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </fieldset>

                        {{-- 4. Keamanan Akun --}}
                        <fieldset class="mb-7">
                            <div class="flex items-center gap-2 pb-2.5 mb-4 border-b border-line">
                                <span class="w-6 h-6 rounded-hospital bg-primary/10 text-primary text-xs font-bold flex items-center justify-center">4</span>
                                <legend class="text-sm font-bold text-ink uppercase tracking-wide">Keamanan Akun Pasien</legend>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="password" class="block text-xs font-semibold text-ink mb-1.5">Kata Sandi <span class="text-rose-600">*</span></label>
                                    <div class="relative">
                                        <input :type="showPassword ? 'text' : 'password'" name="password" id="password"
                                            class="w-full h-10 px-3.5 pr-10 rounded-hospital border border-line text-sm text-ink placeholder:text-ink-muted/50 focus:border-primary focus:ring-1 focus:ring-primary outline-hidden transition-colors @error('password') border-rose-500 @enderror"
                                            placeholder="Minimal 8 karakter" required>
                                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-ink-muted hover:text-ink cursor-pointer">
                                            <span x-text="showPassword ? 'Tutup' : 'Lihat'" class="text-[11px] font-semibold text-primary"></span>
                                        </button>
                                    </div>
                                    @error('password')
                                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-xs font-semibold text-ink mb-1.5">Ulangi Kata Sandi <span class="text-rose-600">*</span></label>
                                    <input :type="showPassword ? 'text' : 'password'" name="password_confirmation" id="password_confirmation"
                                        class="w-full h-10 px-3.5 rounded-hospital border border-line text-sm text-ink placeholder:text-ink-muted/50 focus:border-primary focus:ring-1 focus:ring-primary outline-hidden transition-colors"
                                        placeholder="Ulangi kata sandi" required>
                                </div>
                            </div>
                        </fieldset>

                        {{-- Persetujuan Regulasi Medis --}}
                        <div class="mb-7 p-3.5 rounded-hospital bg-surface-subtle border border-line">
                            <label class="flex items-start gap-2.5 cursor-pointer">
                                <input type="checkbox" name="consent" value="1" {{ old('consent') ? 'checked' : '' }}
                                    class="mt-0.5 rounded-xs w-4 h-4 text-primary focus:ring-primary border-line @error('consent') border-rose-500 @enderror" required>
                                <span class="text-xs text-ink-muted leading-relaxed">
                                    Saya menyatakan data yang dimasukkan adalah benar dan menyetujui penerbitan <strong>Kartu Pasien Digital (e-KIP)</strong> serta pemrosesan data rekam medis sesuai ketentuan perundang-undangan kesehatan RI.
                                </span>
                            </label>
                            @error('consent')
                                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full h-11 inline-flex items-center justify-center gap-2 rounded-hospital bg-primary hover:bg-primary-hover text-white text-sm font-semibold transition-colors shadow-xs cursor-pointer">
                            <span>Daftar &amp; Terbitkan Kartu Pasien Digital</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </form>

                    <div class="mt-6 pt-5 border-t border-line text-center text-xs text-ink-muted">
                        Sudah memiliki akun rekam medis?
                        <a href="{{ route('patient.login') }}" class="font-bold text-primary hover:underline ml-1">Masuk ke Portal Pasien</a>
                    </div>
                </div>

                <!-- RIGHT COLUMN: LIVE e-KIP CARD PREVIEW (Cols 5, Sticky) -->
                <div class="lg:col-span-5 lg:sticky lg:top-8 space-y-4">
                    <div class="bg-white rounded-hospital border border-line shadow-card p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></div>
                                <h2 class="text-xs font-bold text-ink uppercase tracking-wider">Pratinjau Langsung e-KIP</h2>
                            </div>
                            <span class="text-[10px] text-slate-400 font-medium">Terbit Otomatis</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mb-4 leading-relaxed">
                            Kartu fisik virtual ini akan langsung aktif di dasbor Anda begitu pendaftaran berhasil. Format resmi sesuai standar faskes.
                        </p>

                        <!-- Virtual e-KIP Card Preview Box -->
                        <div class="flex justify-center">
                            <div style="width: 100%; max-width: 428px; height: 260px; box-sizing: border-box;"
                                 :class="patientTier === 'subsidi'
                                     ? 'bg-gradient-to-br from-[#064e3b] via-[#047857] to-[#065f46] border-emerald-300/40'
                                     : 'bg-gradient-to-br from-[#0c2e59] via-[#0284c7] to-[#0369a1] border-sky-300/40'"
                                 class="rounded-2xl text-white p-4 sm:px-5 sm:py-4 shadow-xl border select-none relative overflow-hidden flex flex-col justify-between transition-all duration-300">
                                
                                <!-- Watermark Cross -->
                                <div class="absolute -right-4 -bottom-4 pointer-events-none opacity-[0.08] text-white z-0">
                                    <svg class="w-44 h-44" viewBox="0 0 100 100" fill="currentColor">
                                        <rect x="36" y="8" width="28" height="84" rx="8" />
                                        <rect x="8" y="36" width="84" height="28" rx="8" />
                                    </svg>
                                </div>
                                <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/5 to-white/10 pointer-events-none z-0"></div>

                                <!-- Header -->
                                <div class="relative z-10 flex items-center justify-between border-b border-white/20 pb-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-white/20 backdrop-blur-md flex items-center justify-center text-white shrink-0 border border-white/30 shadow-2xs">
                                            <svg class="w-4 h-4" :class="patientTier === 'subsidi' ? 'text-emerald-300' : 'text-sky-300'" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M10 4a1 1 0 011-1h2a1 1 0 011 1v5h5a1 1 0 011 1v2a1 1 0 01-1 1h-5v5a1 1 0 01-1 1h-2a1 1 0 01-1-1v-5H5a1 1 0 01-1-1v-2a1 1 0 011-1h5V4z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-xs font-black uppercase tracking-tight text-white leading-tight">Puskesmas Kota Depok</h3>
                                            <p class="text-[8px] font-medium leading-none mt-0.5" :class="patientTier === 'subsidi' ? 'text-emerald-100' : 'text-sky-100'">Dinas Kesehatan Kota Depok</p>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[8px] font-black uppercase tracking-wider bg-white/20 border border-white/30 text-white shadow-2xs">
                                            e-KIP DIGITAL
                                        </span>
                                    </div>
                                </div>

                                <!-- Middle: Chip & Unique Key -->
                                <div class="relative z-10 flex items-center justify-between py-1">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-6 rounded bg-gradient-to-tr from-amber-300 via-amber-200 to-amber-400 p-0.5 shadow-2xs border border-amber-500/40 flex items-center justify-center">
                                            <div class="w-full h-full border border-amber-600/30 rounded flex items-center justify-around px-0.5">
                                                <div class="w-1.5 h-3 border-r border-amber-600/40"></div>
                                                <div class="w-1.5 h-3 border-l border-amber-600/40"></div>
                                            </div>
                                        </div>
                                        <svg class="w-4 h-4" :class="patientTier === 'subsidi' ? 'text-emerald-200/90' : 'text-sky-200/90'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.393 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                                        </svg>
                                    </div>

                                    <div class="text-right bg-black/25 backdrop-blur-xs px-2 py-0.5 rounded-md border border-white/20 shadow-2xs">
                                        <span class="text-[7px] uppercase tracking-wider block font-semibold leading-tight" :class="patientTier === 'subsidi' ? 'text-emerald-200' : 'text-sky-200'">Kunci Unik Pasien</span>
                                        <span class="text-[10px] font-black font-mono tracking-wider leading-tight mt-0.5 block" :class="patientTier === 'subsidi' ? 'text-emerald-300' : 'text-sky-300'">PKM-DPK-ONLINE</span>
                                    </div>
                                </div>

                                <!-- Nomor Peserta -->
                                <div class="relative z-10 py-0.5">
                                    <span class="text-[8px] font-semibold tracking-widest uppercase block leading-tight" :class="patientTier === 'subsidi' ? 'text-emerald-200' : 'text-sky-200'">Nomor Peserta</span>
                                    <div class="text-base sm:text-lg font-black font-mono tracking-widest text-white leading-normal mt-0.5">
                                        3276 &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;
                                    </div>
                                </div>

                                <!-- Bottom Info -->
                                <div class="relative z-10 border-t border-white/20 pt-2 space-y-1">
                                    <div class="flex justify-between items-center gap-2">
                                        <div class="min-w-0 flex-1">
                                            <span class="font-extrabold text-white text-xs block truncate uppercase leading-normal tracking-wide"
                                                  x-text="name ? name.toUpperCase() : 'NAMA LENGKAP PASIEN'">
                                                NAMA LENGKAP PASIEN
                                            </span>
                                        </div>
                                        <div class="shrink-0 text-right">
                                            <template x-if="patientTier === 'subsidi'">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[8px] font-black bg-emerald-400 text-emerald-950 border border-emerald-300 shadow-2xs">
                                                    Subsidi Penuh (Bebas Biaya Rp 0)
                                                </span>
                                            </template>
                                            <template x-if="patientTier === 'mandiri'">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[8px] font-bold bg-sky-200 text-sky-950 border border-sky-300 shadow-2xs">
                                                    Mandiri / Reguler
                                                </span>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="flex justify-between items-center text-[9px] font-mono leading-normal" :class="patientTier === 'subsidi' ? 'text-emerald-100' : 'text-sky-100'">
                                        <span>NIK: <span x-text="formattedNik">3276 ••••••••••</span></span>
                                        <span>Lahir: <span x-text="formattedDob">DD/MM/YYYY</span></span>
                                    </div>

                                    <div class="text-[9px] truncate leading-normal" :class="patientTier === 'subsidi' ? 'text-emerald-100/90' : 'text-sky-100/90'">
                                        Alamat: <span x-text="address ? address : 'Kota Depok, Jawa Barat'">Kota Depok, Jawa Barat</span>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Explanatory Benefit Box -->
                        <div class="mt-4 p-3 rounded-hospital bg-surface border border-line text-xs space-y-2">
                            <div class="flex items-center gap-1.5 font-bold text-ink text-[11px]">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Kepastian Biaya Otomatis di Setiap Reservasi</span>
                            </div>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                Setelah akun terdaftar, status biaya Anda sudah permanen terhubung dengan kartu e-KIP ini. Saat membuat reservasi poliklinik atau telekonsultasi, Anda <strong>tidak perlu memilih biaya lagi</strong>.
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Footer Small -->
            <div class="mt-8 text-center text-xs text-ink-muted">
                <a href="{{ route('home') }}" class="hover:text-primary transition-colors font-medium">← Kembali ke Halaman Depan</a>
            </div>
        </div>
    </div>

</body>
</html>
