<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\QueueEntry;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TriageAssessmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $healthOfficer;

    protected Service $service;

    protected Patient $patient;

    protected QueueEntry $queueEntry;

    protected function setUp(): void
    {
        parent::setUp();

        $this->healthOfficer = User::factory()->create([
            'role' => 'health_officer',
            'name' => 'Ns. Budi Santoso',
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
            'status' => 'waiting_triage',
        ]);
    }

    public function test_health_officer_can_submit_normal_green_triage(): void
    {
        $payload = [
            'category' => 'hijau',
            'systolic_bp' => 120,
            'diastolic_bp' => 80,
            'heart_rate' => 80,
            'respiratory_rate' => 18,
            'temperature' => 36.8,
            'oxygen_saturation' => 98,
            'consciousness_level' => 'compos_mentis',
            'chief_complaint' => 'Batuk dan pilek 2 hari',
        ];

        $response = $this->actingAs($this->healthOfficer)
            ->post(route('triage.store', $this->queueEntry->id), $payload);

        $response->assertRedirect(route('triage.index'));

        $this->assertDatabaseHas('triage_assessments', [
            'queue_entry_id' => $this->queueEntry->id,
            'assessed_by' => $this->healthOfficer->id,
            'category' => 'hijau',
            'is_immediate_response_triggered' => false,
        ]);

        $this->assertDatabaseHas('queue_entries', [
            'id' => $this->queueEntry->id,
            'priority_level' => 3,
            'status' => 'waiting_call',
        ]);

        $this->assertDatabaseHas('queue_status_histories', [
            'queue_entry_id' => $this->queueEntry->id,
            'to_status' => 'waiting_call',
        ]);
    }

    public function test_health_officer_can_submit_emergency_red_triage_triggering_immediate_priority(): void
    {
        $payload = [
            'category' => 'merah',
            'systolic_bp' => 150,
            'diastolic_bp' => 100,
            'heart_rate' => 120,
            'respiratory_rate' => 30,
            'temperature' => 39.0,
            'oxygen_saturation' => 88,
            'consciousness_level' => 'somnolen',
            'chief_complaint' => 'Sesak napas berat dan kesadaran menurun',
        ];

        $response = $this->actingAs($this->healthOfficer)
            ->post(route('triage.store', $this->queueEntry->id), $payload);

        $response->assertRedirect(route('triage.index'));

        $this->assertDatabaseHas('triage_assessments', [
            'queue_entry_id' => $this->queueEntry->id,
            'category' => 'merah',
            'is_immediate_response_triggered' => true,
        ]);

        $this->assertDatabaseHas('queue_entries', [
            'id' => $this->queueEntry->id,
            'priority_level' => 1,
            'status' => 'waiting_call',
        ]);
    }
}
