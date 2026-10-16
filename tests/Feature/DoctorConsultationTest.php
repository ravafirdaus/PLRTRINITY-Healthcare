<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\QueueEntry;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorConsultationTest extends TestCase
{
    use RefreshDatabase;

    protected User $doctor;

    protected Service $service;

    protected Patient $patient;

    protected QueueEntry $queueEntry;

    protected function setUp(): void
    {
        parent::setUp();

        $this->doctor = User::factory()->create([
            'role' => 'doctor',
            'name' => 'dr. Dian Pratama',
        ]);

        $this->service = Service::create([
            'name' => 'Poli Umum',
            'code' => 'UMU',
            'slot_duration_minutes' => 20,
            'max_capacity_per_slot' => 4,
            'is_active' => true,
        ]);

        $this->patient = Patient::create([
            'nik' => '3201012304950001',
            'name' => 'Ahmad Fauzi',
            'date_of_birth' => '1995-04-23',
            'gender' => 'L',
            'phone' => '081234567890',
        ]);

        $this->queueEntry = QueueEntry::create([
            'queue_code' => 'UMU-001',
            'service_id' => $this->service->id,
            'patient_id' => $this->patient->id,
            'queue_date' => Carbon::today()->format('Y-m-d'),
            'registration_type' => 'walk_in',
            'priority_level' => 3,
            'status' => 'waiting_call',
        ]);
    }

    public function test_doctor_can_call_patient(): void
    {
        $response = $this->actingAs($this->doctor)
            ->post(route('doctor.call', $this->queueEntry->id), [
                'counter' => 'Ruang Poli Umum 1',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('queue_entries', [
            'id' => $this->queueEntry->id,
            'status' => 'called',
            'current_counter' => 'Ruang Poli Umum 1',
        ]);
    }

    public function test_doctor_can_start_and_complete_consultation(): void
    {
        // 1. Call patient
        $this->actingAs($this->doctor)->post(route('doctor.call', $this->queueEntry->id));

        // 2. Start consultation
        $this->actingAs($this->doctor)->post(route('doctor.start', $this->queueEntry->id));

        $this->assertDatabaseHas('queue_entries', [
            'id' => $this->queueEntry->id,
            'status' => 'in_consultation',
        ]);

        // 3. Complete consultation
        $response = $this->actingAs($this->doctor)
            ->post(route('doctor.complete', $this->queueEntry->id), [
                'clinical_notes' => 'Pasien flu biasa, diberikan Paracetamol dan vitamin.',
                'disposition' => 'pulang',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('queue_entries', [
            'id' => $this->queueEntry->id,
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('consultation_sessions', [
            'queue_entry_id' => $this->queueEntry->id,
            'doctor_id' => $this->doctor->id,
            'service_id' => $this->service->id,
            'disposition' => 'pulang',
        ]);
    }
}
