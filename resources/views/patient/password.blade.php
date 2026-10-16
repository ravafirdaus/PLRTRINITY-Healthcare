@extends('patient.layouts.app')

@section('title', 'Ubah Password — PuskesmasFlow')

@section('content')
<div class="max-w-md mx-auto space-y-6">
    <div>
        <h1 class="text-xl sm:text-2xl font-extrabold text-ink tracking-tight">Ubah Password Akun</h1>
        <p class="text-xs sm:text-sm text-slate-500 font-normal mt-0.5">Pastikan kata sandi baru Anda kuat dan sulit ditebak.</p>
    </div>

    <div class="bg-white rounded-hospital border border-line shadow-xs p-6 sm:p-8">
        <form method="POST" action="{{ route('patient.password.update') }}" x-data="{ showPassword: false }">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label for="current_password" class="block text-xs font-bold text-slate-700 uppercase mb-1">Password Lama <span class="text-red-500">*</span></label>
                    <input :type="showPassword ? 'text' : 'password'" name="current_password" id="current_password"
                        class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-1 focus:ring-primary @error('current_password') border-red-500 @enderror" required>
                    @error('current_password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase mb-1">Password Baru <span class="text-red-500">*</span></label>
                    <input :type="showPassword ? 'text' : 'password'" name="password" id="password"
                        class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-1 focus:ring-primary @error('password') border-red-500 @enderror"
                        placeholder="Minimal 8 karakter" required>
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase mb-1">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
                    <input :type="showPassword ? 'text' : 'password'" name="password_confirmation" id="password_confirmation"
                        class="w-full rounded-hospital border-line text-sm focus:border-primary focus:ring-1 focus:ring-primary" required>
                </div>

                <label class="flex items-center cursor-pointer pt-1">
                    <input type="checkbox" @click="showPassword = !showPassword" class="rounded-hospital border-line text-primary focus:ring-primary">
                    <span class="ml-2 text-xs text-slate-600 select-none">Tampilkan password</span>
                </label>
            </div>

            <button type="submit" class="w-full mt-6 bg-primary hover:bg-primary-hover text-white font-semibold py-2.5 px-4 rounded-hospital transition-colors shadow-xs text-xs sm:text-sm">
                Simpan Password Baru
            </button>
        </form>
    </div>

    <div class="text-center">
        <a href="{{ route('patient.profile') }}" class="text-xs font-semibold text-slate-500 hover:text-ink">
            &larr; Kembali ke Profil Saya
        </a>
    </div>
</div>
@endsection
