<x-app-layout>
    <x-slot name="header">
        <h1 class="font-extrabold text-xl sm:text-2xl text-ink tracking-tight">
            {{ __('Pengaturan Akun Staf') }}
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 font-normal mt-0.5">
            Perbarui data kredensial login, kata sandi, dan informasi akun petugas faskes.
        </p>
    </x-slot>

    <div class="space-y-6 max-w-4xl">
        <div class="p-6 sm:p-8 bg-white border border-line rounded-hospital shadow-xs">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-6 sm:p-8 bg-white border border-line rounded-hospital shadow-xs">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="p-6 sm:p-8 bg-white border border-line rounded-hospital shadow-xs">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
