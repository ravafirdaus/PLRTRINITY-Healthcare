<?php

namespace App\Http\Requests;

use App\Helpers\PhoneHelper;
use Illuminate\Foundation\Http\FormRequest;

class PatientRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('phone')) {
            $this->merge([
                'phone' => PhoneHelper::normalize($this->phone),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'nik' => ['required', 'string', 'size:16', 'regex:/^[0-9]{16}$/', 'unique:patients,nik'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'min:10', 'max:15', 'regex:/^0[0-9]+$/', 'unique:patients,phone'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female,L,P'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'bpjs_number' => ['nullable', 'string', 'max:20'],
            'patient_tier' => ['nullable', 'in:subsidi,mandiri'],
            'address' => ['nullable', 'string', 'max:500'],
            'consent' => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'nik.required' => 'NIK wajib diisi.',
            'nik.size' => 'NIK harus tepat 16 digit.',
            'nik.regex' => 'NIK harus berupa 16 digit angka.',
            'nik.unique' => 'NIK ini sudah terdaftar. Silakan login atau hubungi petugas.',
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama terlalu panjang (maksimal 255 karakter).',
            'phone.required' => 'Nomor HP wajib diisi.',
            'phone.min' => 'Nomor HP terlalu pendek.',
            'phone.max' => 'Nomor HP terlalu panjang.',
            'phone.regex' => 'Format nomor HP tidak valid. Gunakan format 08xxxxxxxxxx.',
            'phone.unique' => 'Nomor HP ini sudah terdaftar. Silakan login atau hubungi petugas.',
            'date_of_birth.required' => 'Tanggal lahir wajib diisi.',
            'date_of_birth.date' => 'Format tanggal lahir tidak valid.',
            'date_of_birth.before' => 'Tanggal lahir harus sebelum hari ini.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'gender.in' => 'Jenis kelamin tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'consent.required' => 'Anda harus menyetujui ketentuan penggunaan data.',
            'consent.accepted' => 'Anda harus menyetujui ketentuan penggunaan data.',
        ];
    }
}
