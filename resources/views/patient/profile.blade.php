@extends('patient.layouts.app')

@section('title', 'Profil Saya — PuskesmasFlow')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <h1 class="text-xl sm:text-2xl font-extrabold text-ink tracking-tight">Profil &amp; Kartu Pasien</h1>
        <p class="text-xs sm:text-sm text-slate-500 font-normal mt-0.5">Kelola informasi identitas diri dan nomor kontak yang terdaftar di faskes.</p>
    </div>

    <!-- Kartu Pasien Digital -->
    <div class="space-y-2">
        <span class="text-xs font-bold text-slate-700 uppercase tracking-wider block">Kartu Pasien Digital Anda:</span>
        <x-patient-digital-card :patient="$patient" />
    </div>

    <div class="bg-white rounded-hospital border border-line shadow-xs p-6 sm:p-8">
        <form method="POST" action="{{ route('patient.profile.update') }}">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor Induk Kependudukan (NIK)</label>
                    <p class="text-slate-600 bg-surface border border-line rounded-hospital px-3 py-2 text-sm font-mono">{{ $patient->masked_nik }}</p>
                    <p class="text-[11px] text-slate-400 mt-1">NIK terproteksi dan tidak dapat diubah secara mandiri. Hubungi petugas loket faskes jika ada perubahan.</p>
                </div>

                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $patient->name) }}"
                        class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-1 focus:ring-primary @error('name') border-red-500 @enderror" required>
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor WhatsApp / HP <span class="text-red-500">*</span></label>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone', $patient->phone) }}"
                        class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-1 focus:ring-primary @error('phone') border-red-500 @enderror" required>
                    <p class="text-[11px] text-slate-400 mt-1">Nomor HP digunakan untuk login pasien. Perubahan akan berlaku pada login berikutnya.</p>
                    @error('phone')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tanggal Lahir</label>
                        <p class="text-slate-600 bg-surface border border-line rounded-hospital px-3 py-2 text-sm">
                            {{ $patient->date_of_birth ? $patient->date_of_birth->isoFormat('D MMMM Y') : '-' }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Jenis Kelamin</label>
                        <p class="text-slate-600 bg-surface border border-line rounded-hospital px-3 py-2 text-sm">
                            {{ ($patient->gender === 'male' || $patient->gender === 'L') ? 'Laki-laki' : 'Perempuan' }}
                        </p>
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-xs font-bold text-slate-700 uppercase mb-1">Alamat Domisili <span class="text-slate-400 text-xs font-normal">(opsional)</span></label>
                    <textarea name="address" id="address" rows="2"
                        class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-1 focus:ring-primary @error('address') border-red-500 @enderror">{{ old('address', $patient->address) }}</textarea>
                    @error('address')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="bpjs_number" class="block text-xs font-bold text-slate-700 uppercase mb-1">No. Kartu BPJS Kesehatan <span class="text-slate-400 text-xs font-normal">(opsional)</span></label>
                    <input type="text" name="bpjs_number" id="bpjs_number" value="{{ old('bpjs_number', $patient->bpjs_number) }}"
                        class="w-full rounded-hospital border-line text-sm font-mono focus:border-primary focus:ring-1 focus:ring-primary @error('bpjs_number') border-red-500 @enderror"
                        placeholder="Contoh: 0001234567890">
                    @error('bpjs_number')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4 pt-4 border-t border-line">
                <button type="submit" class="w-full sm:w-auto bg-primary hover:bg-primary-hover text-white font-semibold text-xs sm:text-sm py-2.5 px-5 rounded-hospital transition-colors shadow-xs">
                    Simpan Perubahan
                </button>
                <a href="{{ route('patient.password') }}" class="text-primary hover:text-primary-hover text-xs sm:text-sm font-semibold">
                    Ubah Password Akun &rarr;
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
