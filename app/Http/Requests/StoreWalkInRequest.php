<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWalkInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nik' => ['required', 'string', 'size:16'],
            'name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:today'],
            'gender' => ['required', 'in:L,P'],
            'phone' => ['required', 'string', 'min:9', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'bpjs_number' => ['nullable', 'string', 'max:30'],
            'service_id' => ['required', 'exists:services,id'],
            'is_emergency' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nik.required' => 'Nomor Induk Kependudukan (NIK) wajib diisi.',
            'nik.size' => 'NIK harus berjumlah 16 digit angka.',
            'name.required' => 'Nama lengkap pasien wajib diisi.',
            'date_of_birth.required' => 'Tanggal lahir pasien wajib diisi.',
            'gender.required' => 'Jenis kelamin pasien wajib dipilih.',
            'phone.required' => 'Nomor telepon/WhatsApp aktif wajib diisi.',
            'service_id.required' => 'Poli tujuan wajib dipilih.',
        ];
    }
}
