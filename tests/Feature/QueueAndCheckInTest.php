<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\ClinicSetting;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QueueAndCheckInTest extends TestCase
{
    use RefreshDatabase;

    protected User $registrar;

    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        ClinicSetting::set('late_tolerance_minutes', 30);
        ClinicSetting::set('default_slot_duration_minutes', 20);

        $this->registrar = User::factory()->create([
            'role' => 'registrar',
            'name' => 'Petugas Loket',
        ]);

        $this->service = Service::create([
            'name' => 'Poli Umum',
            'code' => 'UMU',
            'slot_duration_minutes' => 20,
            'max_capacity_per_slot' => 4,
            'is_active' => true,
        ]);
    }

    public function test_registrar_can_check_in_booked_appointment(): void
    {
        $patient = Patient::create([
            'nik' => '3201012304950001',
            'name' => 'Ahmad Fauzi',
            'date_of_birth' => '1995-04-23',
            'gender' => 'L',
            'phone' => '081234567890',
        ]);

        $appointment = Appointment::create([
            'appointment_number' => 'RES-20261013-0001',
            'patient_id' => $patient->id,
            'service_id' => $this->service->id,
            'appointment_date' => Carbon::today()->format('Y-m-d'),
            'slot_time' => Carbon::now()->format('H:i:00'),
            'status' => 'booked',
        ]);

        $response = $this->actingAs($this->registrar)
            ->post(route('registrar.check_in', $appointment->id));

        $response->assertRedirect();

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'checked_in',
        ]);

        $this->assertDatabaseHas('queue_entries', [
            'service_id' => $this->service->id,
            'patient_id' => $patient->id,
            'queue_code' => 'UMU-001',
            'status' => 'waiting_call',
        ]);
    }

    public function test_registrar_can_check_in_a_future_slot_during_the_same_local_day(): void
    {
        $this->travelTo(Carbon::parse('2026-10-15 06:08:00', 'Asia/Jakarta'));

        $patient = Patient::create([
            'nik' => '3201012304950011',
            'name' => 'Siti Aminah',
            'date_of_birth' => '1995-04-23',
            'gender' => 'P',
            'phone' => '081234567890',
        ]);

        $appointment = Appointment::create([
            'appointment_number' => 'RES-20261015-0001',
            'patient_id' => $patient->id,
            'service_id' => $this->service->id,
            'appointment_date' => '2026-10-15',
            'slot_time' => '12:20:00',
            'status' => 'booked',
        ]);

        $response = $this->actingAs($this->registrar)
            ->post(route('registrar.check_in', $appointment->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'checked_in',
        ]);
    }

    public function test_late_check_in_persists_no_show_status(): void
    {
        ClinicSetting::set('late_tolerance_minutes', 15);
        $this->travelTo(Carbon::parse('2026-10-15 10:16:00', 'Asia/Jakarta'));

        $patient = Patient::create([
            'nik' => '3201012304950012',
            'name' => 'Budi Santoso',
            'date_of_birth' => '1995-04-23',
            'gender' => 'L',
            'phone' => '081234567890',
        ]);

        $appointment = Appointment::create([
            'appointment_number' => 'RES-20261015-0002',
            'patient_id' => $patient->id,
            'service_id' => $this->service->id,
            'appointment_date' => '2026-10-15',
            'slot_time' => '10:00:00',
            'status' => 'booked',
        ]);

        $response = $this->actingAs($this->registrar)
            ->post(route('registrar.check_in', $appointment->id));

        $response->assertSessionHasErrors(['error']);
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'no_show',
        ]);
        $this->assertDatabaseMissing('queue_entries', [
            'appointment_id' => $appointment->id,
        ]);
    }

    public function test_registrar_can_register_walk_in_patient(): void
    {
        $payload = [
            'nik' => '3201012304950002',
            'name' => 'Dewi Lestari',
            'date_of_birth' => '1990-01-15',
            'gender' => 'P',
            'phone' => '081234567892',
            'service_id' => $this->service->id,
        ];

        $response = $this->actingAs($this->registrar)
            ->post(route('registrar.walk_in'), $payload);

        $response->assertRedirect();

        $this->assertDatabaseHas('patients', ['nik' => '3201012304950002']);
        $this->assertDatabaseHas('queue_entries', [
            'service_id' => $this->service->id,
            'registration_type' => 'walk_in',
            'priority_level' => 3,
            'status' => 'waiting_triage',
        ]);
    }

    public function test_registrar_can_register_emergency_patient_with_priority_1(): void
    {
        $payload = [
            'nik' => '3201012304950003',
            'name' => 'Pasien Gawat',
            'date_of_birth' => '1985-06-20',
            'gender' => 'L',
            'phone' => '081234567893',
            'service_id' => $this->service->id,
            'is_emergency' => 1,
        ];

        $response = $this->actingAs($this->registrar)
            ->post(route('registrar.walk_in'), $payload);

        $response->assertRedirect();

        $this->assertDatabaseHas('queue_entries', [
            'service_id' => $this->service->id,
            'registration_type' => 'emergency',
            'priority_level' => 1,
            'status' => 'waiting_call',
            'queue_code' => 'EMG-001',
        ]);
    }
}
