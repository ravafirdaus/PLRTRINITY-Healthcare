<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PatientReservationTest extends TestCase
{
    use RefreshDatabase;

    protected User $patientUser;

    protected Patient $patient;

    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->patientUser = User::create([
            'name' => 'Ahmad Fauzi',
            'email' => 'patient_081298765431@puskesmasflow.local',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'phone' => '081298765431',
        ]);

        $this->patient = Patient::create([
            'user_id' => $this->patientUser->id,
            'nik' => '3201012304950001',
            'name' => 'Ahmad Fauzi',
            'date_of_birth' => '1995-04-23',
            'gender' => 'L',
            'phone' => '081298765431',
            'address' => 'Jl. Merdeka No. 12',
            'bpjs_number' => '0001234567891',
        ]);

        $this->service = Service::create([
            'name' => 'Poli Umum',
            'code' => 'UMU',
            'slot_duration_minutes' => 20,
            'max_capacity_per_slot' => 4,
            'is_active' => true,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_patient_dashboard(): void
    {
        $response = $this->get(route('patient.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_patient_can_view_dashboard(): void
    {
        $response = $this->actingAs($this->patientUser)->get(route('patient.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Ahmad Fauzi');
        $response->assertSee($this->patient->masked_nik);
    }

    public function test_patient_can_create_reservation_without_reentering_profile(): void
    {
        $date = Carbon::tomorrow()->format('Y-m-d');

        $response = $this->actingAs($this->patientUser)->post(route('patient.reservasi.store'), [
            'service_id' => $this->service->id,
            'appointment_date' => $date,
            'slot_time' => '09:00:00',
            'notes' => 'Pusing dan demam.',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('appointments', [
            'patient_id' => $this->patient->id,
            'service_id' => $this->service->id,
            'status' => 'booked',
            'notes' => 'Pusing dan demam.',
        ]);
    }

    public function test_patient_cannot_double_book_same_service_and_date(): void
    {
        $date = Carbon::tomorrow()->format('Y-m-d');

        // First appointment
        $this->actingAs($this->patientUser)->post(route('patient.reservasi.store'), [
            'service_id' => $this->service->id,
            'appointment_date' => $date,
            'slot_time' => '09:00:00',
        ]);

        // Attempt second appointment for same service & date
        $response = $this->actingAs($this->patientUser)->post(route('patient.reservasi.store'), [
            'service_id' => $this->service->id,
            'appointment_date' => $date,
            'slot_time' => '09:20:00',
        ]);

        $response->assertSessionHasErrors(['slot_time']);
    }

    public function test_patient_can_cancel_booked_appointment(): void
    {
        $date = Carbon::tomorrow()->format('Y-m-d');

        $appointment = Appointment::create([
            'appointment_number' => 'RES-TEST-0001',
            'patient_id' => $this->patient->id,
            'service_id' => $this->service->id,
            'appointment_date' => $date,
            'slot_time' => '09:00:00',
            'status' => 'booked',
        ]);

        $response = $this->actingAs($this->patientUser)
            ->patch(route('patient.reservasi.cancel', $appointment));

        $response->assertRedirect(route('patient.dashboard'));

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_patient_can_rebook_same_service_and_date_after_cancellation(): void
    {
        $date = Carbon::tomorrow()->format('Y-m-d');

        $appointment = Appointment::create([
            'appointment_number' => 'RES-TEST-0001',
            'patient_id' => $this->patient->id,
            'service_id' => $this->service->id,
            'appointment_date' => $date,
            'slot_time' => '09:00:00',
            'status' => 'booked',
        ]);

        // Cancel first appointment
        $this->actingAs($this->patientUser)
            ->patch(route('patient.reservasi.cancel', $appointment))
            ->assertRedirect();

        // Attempt new reservation for same service and date on a different slot
        $response = $this->actingAs($this->patientUser)->post(route('patient.reservasi.store'), [
            'service_id' => $this->service->id,
            'appointment_date' => $date,
            'slot_time' => '10:00:00',
            'notes' => 'Jadwal ulang setelah batal.',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('appointments', [
            'patient_id' => $this->patient->id,
            'service_id' => $this->service->id,
            'slot_time' => '10:00:00',
            'status' => 'booked',
            'notes' => 'Jadwal ulang setelah batal.',
        ]);
    }

    public function test_patient_cannot_view_or_cancel_other_patients_appointment(): void
    {
        $otherUser = User::create([
            'name' => 'Other Patient',
            'email' => 'other@puskesmasflow.local',
            'password' => Hash::make('password'),
            'role' => 'patient',
            'phone' => '081299990000',
        ]);

        $otherPatient = Patient::create([
            'user_id' => $otherUser->id,
            'nik' => '3201019999990009',
            'name' => 'Other Patient',
            'date_of_birth' => '1990-01-01',
            'gender' => 'L',
            'phone' => '081299990000',
        ]);

        $appointment = Appointment::create([
            'appointment_number' => 'RES-TEST-9999',
            'patient_id' => $otherPatient->id,
            'service_id' => $this->service->id,
            'appointment_date' => Carbon::tomorrow()->format('Y-m-d'),
            'slot_time' => '10:00:00',
            'status' => 'booked',
        ]);

        // Attempt view
        $viewResponse = $this->actingAs($this->patientUser)
            ->get(route('patient.reservasi.show', $appointment));
        $viewResponse->assertStatus(403);

        // Attempt cancel
        $cancelResponse = $this->actingAs($this->patientUser)
            ->patch(route('patient.reservasi.cancel', $appointment));
        $cancelResponse->assertStatus(403);
    }
}
