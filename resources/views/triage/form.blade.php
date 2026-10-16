<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-ink tracking-tight">
                    {{ __('Formulir Penilaian Triase Klinis') }}
                </h1>
                <p class="text-xs text-ink-muted mt-0.5">
                    Nomor Antrean: <span class="font-mono text-primary font-bold">{{ $queueEntry->queue_code }}</span> &bull; Pasien: <strong class="text-ink">{{ $queueEntry->patient->name }}</strong>
                </p>
            </div>
            <a href="{{ route('triage.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-hospital bg-surface-subtle hover:bg-slate-100 text-ink text-xs font-semibold border border-line transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Daftar</span>
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <form action="{{ route('triage.store', $queueEntry->id) }}" method="POST"
              x-data="{ category: '{{ old('category', $queueEntry->triageAssessment->category ?? 'hijau') }}' }"
              class="bg-white rounded-hospital p-6 sm:p-8 border border-line shadow-card space-y-8">
            @csrf

            <!-- Ringkasan Pasien -->
            <div class="p-4 rounded-hospital bg-surface-subtle border border-line grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                <div>
                    <span class="text-ink-muted block text-[11px]">Nama Pasien</span>
                    <span class="font-bold text-sm text-ink">{{ $queueEntry->patient->name }}</span>
                </div>
                <div>
                    <span class="text-ink-muted block text-[11px]">NIK / Usia</span>
                    <span class="font-bold text-ink font-mono">{{ $queueEntry->patient->nik }} ({{ $queueEntry->patient->age }} th)</span>
                </div>
                <div>
                    <span class="text-ink-muted block text-[11px]">Jenis Kelamin</span>
                    <span class="font-bold text-ink">{{ $queueEntry->patient->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                </div>
                <div>
                    <span class="text-ink-muted block text-[11px]">Poli Tujuan</span>
                    <span class="font-bold text-accent">{{ $queueEntry->service->name }}</span>
                </div>
            </div>

            <!-- Bagian 1: Tanda-Tanda Vital -->
            <div>
                <div class="flex items-center gap-2 pb-2 mb-4 border-b border-line">
                    <span class="w-5 h-5 rounded-hospital bg-primary/10 text-primary text-xs font-bold flex items-center justify-center">1</span>
                    <h2 class="text-xs font-bold text-ink uppercase tracking-wide">Pemeriksaan Tanda Vital</h2>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1.5">Tekanan Darah Sistolik</label>
                        <div class="relative">
                            <input type="number" name="systolic_bp" value="{{ old('systolic_bp', $queueEntry->triageAssessment->systolic_bp ?? '') }}"
                                   placeholder="120" min="40" max="300"
                                   class="w-full h-10 px-3 pr-14 rounded-hospital border border-line text-sm text-ink font-mono focus:border-primary focus:ring-1 focus:ring-primary outline-hidden">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-ink-muted font-mono">mmHg</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1.5">Tekanan Darah Diastolik</label>
                        <div class="relative">
                            <input type="number" name="diastolic_bp" value="{{ old('diastolic_bp', $queueEntry->triageAssessment->diastolic_bp ?? '') }}"
                                   placeholder="80" min="20" max="200"
                                   class="w-full h-10 px-3 pr-14 rounded-hospital border border-line text-sm text-ink font-mono focus:border-primary focus:ring-1 focus:ring-primary outline-hidden">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-ink-muted font-mono">mmHg</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1.5">Denyut Nadi (Heart Rate)</label>
                        <div class="relative">
                            <input type="number" name="heart_rate" value="{{ old('heart_rate', $queueEntry->triageAssessment->heart_rate ?? '') }}"
                                   placeholder="80" min="30" max="250"
                                   class="w-full h-10 px-3 pr-12 rounded-hospital border border-line text-sm text-ink font-mono focus:border-primary focus:ring-1 focus:ring-primary outline-hidden">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-ink-muted font-mono">bpm</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1.5">Laju Pernapasan (RR)</label>
                        <div class="relative">
                            <input type="number" name="respiratory_rate" value="{{ old('respiratory_rate', $queueEntry->triageAssessment->respiratory_rate ?? '') }}"
                                   placeholder="20" min="5" max="80"
                                   class="w-full h-10 px-3 pr-14 rounded-hospital border border-line text-sm text-ink font-mono focus:border-primary focus:ring-1 focus:ring-primary outline-hidden">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-ink-muted font-mono">x/mnt</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1.5">Suhu Tubuh</label>
                        <div class="relative">
                            <input type="number" step="0.1" name="temperature" value="{{ old('temperature', $queueEntry->triageAssessment->temperature ?? '') }}"
                                   placeholder="36.5" min="30" max="45"
                                   class="w-full h-10 px-3 pr-10 rounded-hospital border border-line text-sm text-ink font-mono focus:border-primary focus:ring-1 focus:ring-primary outline-hidden">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-ink-muted font-mono">°C</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1.5">Saturasi Oksigen (SpO2)</label>
                        <div class="relative">
                            <input type="number" name="oxygen_saturation" value="{{ old('oxygen_saturation', $queueEntry->triageAssessment->oxygen_saturation ?? '') }}"
                                   placeholder="98" min="50" max="100"
                                   class="w-full h-10 px-3 pr-8 rounded-hospital border border-line text-sm text-ink font-mono focus:border-primary focus:ring-1 focus:ring-primary outline-hidden">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-ink-muted font-mono">%</span>
                        </div>
                    </div>

                    <div class="col-span-2 sm:col-span-3">
                        <label class="block text-xs font-semibold text-ink mb-1.5">Tingkat Kesadaran Pasien <span class="text-rose-600">*</span></label>
                        <select name="consciousness_level" required class="w-full h-10 px-3 rounded-hospital border border-line text-sm text-ink focus:border-primary focus:ring-1 focus:ring-primary outline-hidden">
                            @php $cLevel = old('consciousness_level', $queueEntry->triageAssessment->consciousness_level ?? 'compos_mentis'); @endphp
                            <option value="compos_mentis" {{ $cLevel === 'compos_mentis' ? 'selected' : '' }}>Compos Mentis (Sadar Penuh & Responsif)</option>
                            <option value="somnolen" {{ $cLevel === 'somnolen' ? 'selected' : '' }}>Somnolen (Mengantuk Berat / Respons Melambat)</option>
                            <option value="sopor" {{ $cLevel === 'sopor' ? 'selected' : '' }}>Sopor (Hanya Respons Rangsang Nyeri)</option>
                            <option value="coma" {{ $cLevel === 'coma' ? 'selected' : '' }}>Coma (Tidak Ada Respons Sama Sekali)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Bagian 2: Keluhan & Pengamatan -->
            <div>
                <div class="flex items-center gap-2 pb-2 mb-4 border-b border-line">
                    <span class="w-5 h-5 rounded-hospital bg-primary/10 text-primary text-xs font-bold flex items-center justify-center">2</span>
                    <h2 class="text-xs font-bold text-ink uppercase tracking-wide">Anamnesis & Keluhan Utama</h2>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1.5">Keluhan Utama Pasien <span class="text-rose-600">*</span></label>
                        <textarea name="chief_complaint" rows="3" required placeholder="Jelaskan keluhan utama pasien secara ringkas dan klinis..."
                                  class="w-full p-3 rounded-hospital border border-line text-sm text-ink focus:border-primary focus:ring-1 focus:ring-primary outline-hidden">{{ old('chief_complaint', $queueEntry->triageAssessment->chief_complaint ?? $queueEntry->appointment->notes ?? '') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1.5">Catatan Observasi Fisik (Opsional)</label>
                        <textarea name="observations" rows="2" placeholder="Tanda bahaya, luka luar, pucat, riwayat alergi, atau catatan klinis lainnya..."
                                  class="w-full p-3 rounded-hospital border border-line text-sm text-ink focus:border-primary focus:ring-1 focus:ring-primary outline-hidden">{{ old('observations', $queueEntry->triageAssessment->observations ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Bagian 3: Penetapan Kategori Triase Klinis -->
            <div>
                <div class="flex items-center gap-2 pb-2 mb-2 border-b border-line">
                    <span class="w-5 h-5 rounded-hospital bg-primary/10 text-primary text-xs font-bold flex items-center justify-center">3</span>
                    <h2 class="text-xs font-bold text-ink uppercase tracking-wide">Penetapan Kategori Triase</h2>
                </div>
                <p class="text-xs text-ink-muted mb-4">Pilih kategori prioritas klinis. Sistem akan otomatis mengatur urutan prioritas antrean pada layar dokter poli.</p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- MERAH -->
                    <label class="relative flex flex-col p-4 rounded-hospital border cursor-pointer transition-all"
                           :class="category === 'merah' ? 'border-rose-500 bg-rose-50/70 shadow-xs ring-1 ring-rose-500' : 'border-line hover:border-rose-300'">
                        <input type="radio" name="category" value="merah" x-model="category" class="sr-only">
                        <div class="flex items-center justify-between mb-2">
                            <span class="px-2 py-0.5 rounded-hospital bg-rose-600 text-white font-bold text-[11px] uppercase">MERAH</span>
                            <span class="text-[10px] font-bold text-rose-700 font-mono">Prioritas 1</span>
                        </div>
                        <div class="font-bold text-sm text-ink">Resusitasi / Gawat Darurat</div>
                        <p class="text-[11px] text-ink-muted mt-1 leading-relaxed">
                            Kondisi kritis mengancam nyawa, sesak napas berat, syok, kejang, henti napas/jantung.
                        </p>
                    </label>

                    <!-- KUNING -->
                    <label class="relative flex flex-col p-4 rounded-hospital border cursor-pointer transition-all"
                           :class="category === 'kuning' ? 'border-amber-500 bg-amber-50/70 shadow-xs ring-1 ring-amber-500' : 'border-line hover:border-amber-300'">
                        <input type="radio" name="category" value="kuning" x-model="category" class="sr-only">
                        <div class="flex items-center justify-between mb-2">
                            <span class="px-2 py-0.5 rounded-hospital bg-amber-500 text-white font-bold text-[11px] uppercase">KUNING</span>
                            <span class="text-[10px] font-bold text-amber-700 font-mono">Prioritas 2</span>
                        </div>
                        <div class="font-bold text-sm text-ink">Mendesak (Urgent)</div>
                        <p class="text-[11px] text-ink-muted mt-1 leading-relaxed">
                            Potensi perburukan cepat, nyeri sedang-berat, demam tinggi anak, risiko dehidrasi.
                        </p>
                    </label>

                    <!-- HIJAU -->
                    <label class="relative flex flex-col p-4 rounded-hospital border cursor-pointer transition-all"
                           :class="category === 'hijau' ? 'border-emerald-600 bg-emerald-50/70 shadow-xs ring-1 ring-emerald-600' : 'border-line hover:border-emerald-300'">
                        <input type="radio" name="category" value="hijau" x-model="category" class="sr-only">
                        <div class="flex items-center justify-between mb-2">
                            <span class="px-2 py-0.5 rounded-hospital bg-emerald-600 text-white font-bold text-[11px] uppercase">HIJAU</span>
                            <span class="text-[10px] font-bold text-emerald-700 font-mono">Prioritas 3</span>
                        </div>
                        <div class="font-bold text-sm text-ink">Tidak Mendesak</div>
                        <p class="text-[11px] text-ink-muted mt-1 leading-relaxed">
                            Kondisi stabil, keluhan ringan sampai sedang (batuk, flu, gatal, kontrol rutin).
                        </p>
                    </label>
                </div>

                <!-- Peringatan Kasus Darurat (Merah) -->
                <div x-show="category === 'merah'" class="mt-4 p-3.5 rounded-hospital bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-3">
                    <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <div>
                        <strong class="block font-bold">JALUR SEGERA (IMMEDIATE RESPONSE):</strong>
                        Kategori MERAH akan langsung memprioritaskan pasien ini ke urutan teratas pada daftar pemeriksaan dokter poli.
                    </div>
                </div>
            </div>

            <!-- Tombol Submit -->
            <div class="pt-5 border-t border-line flex items-center justify-between">
                <a href="{{ route('triage.index') }}" class="text-xs font-semibold text-ink-muted hover:text-ink">
                    Batal
                </a>
                <button type="submit" class="h-11 px-6 bg-primary hover:bg-primary-hover text-white font-semibold text-sm rounded-hospital shadow-xs transition-colors flex items-center gap-2">
                    <span>Simpan Penilaian & Masukkan ke Antrean Poli</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

