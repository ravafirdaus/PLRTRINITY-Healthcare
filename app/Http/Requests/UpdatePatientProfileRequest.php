<?php

namespace App\Http\Requests;

use App\Helpers\PhoneHelper;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientProfileRequest extends FormRequest
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
        $patientId = auth()->user()->patient->id ?? null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'min:10', 'max:15', 'regex:/^0[0-9]+$/',
                'unique:patients,phone,'.$patientId],
            'address' => ['nullable', 'string', 'max:500'],
            'bpjs_number' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'phone.required' => 'Nomor HP wajib diisi.',
            'phone.min' => 'Nomor HP terlalu pendek.',
            'phone.unique' => 'Nomor HP ini sudah digunakan akun lain.',
            'phone.regex' => 'Format nomor HP tidak valid.',
        ];
    }
}
