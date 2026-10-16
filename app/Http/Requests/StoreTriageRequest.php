<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTriageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->role === 'health_officer' || $this->user()->role === 'admin');
    }

    public function rules(): array
    {
        return [
            'category' => ['required', 'in:merah,kuning,hijau,hitam'],
            'chief_complaint' => ['required', 'string', 'max:1000'],
            'consciousness_level' => ['required', 'in:compos_mentis,somnolen,sopor,coma'],
            'systolic_bp' => ['nullable', 'integer', 'between:40,300'],
            'diastolic_bp' => ['nullable', 'integer', 'between:20,200'],
            'heart_rate' => ['nullable', 'integer', 'between:30,250'],
            'respiratory_rate' => ['nullable', 'integer', 'between:5,80'],
            'temperature' => ['nullable', 'numeric', 'between:30,45'],
            'oxygen_saturation' => ['nullable', 'integer', 'between:50,100'],
            'observations' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'category.required' => 'Kategori triase wajib ditentukan oleh petugas.',
            'chief_complaint.required' => 'Keluhan utama pasien wajib dicatat.',
            'consciousness_level.required' => 'Tingkat kesadaran pasien wajib dipilih.',
        ];
    }
}
