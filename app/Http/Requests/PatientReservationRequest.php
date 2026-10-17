<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PatientReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isPatient();
    }

    public function rules(): array
    {
        return [
            'service_id' => ['required', 'exists:services,id'],
            'doctor_id' => ['nullable', 'exists:users,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'slot_time' => ['required', 'regex:/^([01][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'],
            'consultation_type' => ['nullable', 'string', 'in:in_person,video'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'service_id.required' => 'Pilih poli atau layanan tujuan.',
            'service_id.exists' => 'Layanan yang dipilih tidak tersedia.',
            'appointment_date.required' => 'Tanggal kunjungan wajib dipilih.',
            'appointment_date.after_or_equal' => 'Tanggal kunjungan harus hari ini atau setelahnya.',
            'slot_time.required' => 'Pilih waktu kunjungan.',
            'slot_time.date_format' => 'Format waktu tidak valid.',
            'consultation_type.in' => 'Metode konsultasi harus tatap muka atau video.',
            'notes.max' => 'Catatan/keluhan terlalu panjang (maksimal 1000 karakter).',
        ];
    }
}
