<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->role === 'doctor' || $this->user()->role === 'admin');
    }

    public function rules(): array
    {
        return [
            'disposition' => ['required', 'in:pulang,rujuk_lanjut,rawat_inap,kontrol_ulang'],
            'clinical_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'disposition.required' => 'Disposisi akhir pasien wajib ditentukan.',
        ];
    }
}
