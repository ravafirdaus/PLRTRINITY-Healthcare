<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Service;
use App\Models\StaffSchedule;
use App\Models\Teleconsultation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TeleconsultationTest extends TestCase
{
    use RefreshDatabase;

    protected User $patientUser;

    protected Patient $patient;

    protected User $otherPatientUser;

    protected Patient $otherPatient;

    protected User $doctor;

    protected User $unauthorizedDoctor;

    protected Service $telemedicineService;

    protected Service $inPersonOnlyService;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Patient User 1
        $this->patientUser = User::create([
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad@example.com',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'phone' => '081234567890',
        ]);

        $this->patient = Patient::create([
            'user_id' => $this->patientUser->id,
            'nik' => '3201012304950001',
            'name' => 'Ahmad Fauzi',
            'date_of_birth' => '1995-04-23',
            'gender' => 'L',
            'phone' => '081234567890',
        ]);

        // 2. Patient User 2
        $this->otherPatientUser = User::create([
            'name' => 'Dewi Lestari',
            'email' => 'dewi@example.com',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'phone' => '081234567899',
        ]);

        $this->otherPatient = Patient::create([
            'user_id' => $this->otherPatientUser->id,
            'nik' => '3201015508880002',
            'name' => 'Dewi Lestari',
            'date_of_birth' => '1988-08-15',
            'gender' => 'P',
            'phone' => '081234567899',
        ]);

        // 3. Authorized Doctor
        $this->doctor = User::create([
            'name' => 'dr. Dian Pratama',
            'email' => 'dokter@puskesmasflow.local',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'phone' => '081234567891',
        ]);

        // 4. Unauthorized Doctor
        $this->unauthorizedDoctor = User::create([
            'name' => 'drg. Maya Ananda',
            'email' => 'dokter.lain@puskesmasflow.local',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'phone' => '081234567892',
        ]);

        // 5. Service supporting teleconsultation
        $this->telemedicineService = Service::create([
            'name' => 'Poli Umum',
            'code' => 'UMU',
            'slot_duration_minutes' => 20,
            'max_capacity_per_slot' => 4,
            'is_active' => true,
            'supports_teleconsultation' => true,
        ]);

        // 6. Service NOT supporting teleconsultation
        $this->inPersonOnlyService = Service::create([
            'name' => 'Poli Gigi & Mulut',
            'code' => 'GIG',
            'slot_duration_minutes' => 25,
            'max_capacity_per_slot' => 3,
            'is_active' => true,
            'supports_teleconsultation' => false,
        ]);

        // Assign doctor schedule to telemedicine service
        StaffSchedule::create([
            'user_id' => $this->doctor->id,
            'service_id' => $this->telemedicineService->id,
            'day_of_week' => Carbon::now()->dayOfWeekIso,
            'start_time' => '08:00',
            'end_time' => '16:00',
            'is_active' => true,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_teleconsultation_routes(): void
    {
        $response = $this->get(route('patient.telekonsultasi.index'));
        $response->assertRedirect(route('login'));

        $responseDoctor = $this->get(route('doctor.telekonsultasi.index'));
        $responseDoctor->assertRedirect(route('login'));
    }

    public function test_service_without_teleconsultation_support_rejects_video_booking(): void
    {
        $date = Carbon::tomorrow()->format('Y-m-d');

        $response = $this->actingAs($this->patientUser)->post(route('patient.reservasi.store'), [
            'service_id' => $this->inPersonOnlyService->id,
            'appointment_date' => $date,
            'slot_time' => '09:00',
            'consultation_type' => 'video',
            'notes' => 'Keluhan batuk pilek',
        ]);

        $response->assertSessionHasErrors('consultation_type');
        $this->assertDatabaseMissing('appointments', [
            'patient_id' => $this->patient->id,
            'consultation_type' => 'video',
        ]);
    }

    public function test_patient_can_book_video_teleconsultation_and_creates_teleconsultation_record(): void
    {
        $date = Carbon::tomorrow()->format('Y-m-d');

        $response = $this->actingAs($this->patientUser)->post(route('patient.reservasi.store'), [
            'service_id' => $this->telemedicineService->id,
            'appointment_date' => $date,
            'slot_time' => '09:00',
            'consultation_type' => 'video',
            'notes' => 'Konsultasi keluhan pusing dan demam.',
        ]);

        $appointment = Appointment::where('patient_id', $this->patient->id)->first();
        $this->assertNotNull($appointment);
        $this->assertEquals('video', $appointment->consultation_type);

        $response->assertRedirect(route('patient.reservasi.show', $appointment));

        $teleconsultation = Teleconsultation::where('appointment_id', $appointment->id)->first();
        $this->assertNotNull($teleconsultation);
        $this->assertEquals('scheduled', $teleconsultation->status);
        $this->assertNotNull($teleconsultation->room_name);
        $this->assertStringStartsWith(config('jitsi.room_prefix', 'pkflow_'), $teleconsultation->room_name);
        // Room name must not contain patient NIK, phone, or name
        $this->assertStringNotContainsString($this->patient->nik, $teleconsultation->room_name);
        $this->assertStringNotContainsString($this->patient->name, $teleconsultation->room_name);
    }

    public function test_patient_can_view_own_teleconsultation(): void
    {
        $tele = $this->createTeleconsultation($this->patient, $this->telemedicineService);

        $response = $this->actingAs($this->patientUser)->get(route('patient.telekonsultasi.show', $tele));

        $response->assertStatus(200);
        $response->assertSee($this->telemedicineService->name);
        $response->assertSee($this->patient->name);
    }

    public function test_patient_cannot_view_another_patients_teleconsultation(): void
    {
        $otherTele = $this->createTeleconsultation($this->otherPatient, $this->telemedicineService);

        $response = $this->actingAs($this->patientUser)->get(route('patient.telekonsultasi.show', $otherTele));

        $response->assertStatus(403);
    }

    public function test_doctor_cannot_enter_session_not_assigned_or_scheduled_for_them(): void
    {
        $tele = $this->createTeleconsultation($this->patient, $this->telemedicineService, $this->doctor);

        // Unauthorized doctor tries to join room
        $response = $this->actingAs($this->unauthorizedDoctor)->get(route('doctor.telekonsultasi.room', $tele));

        $response->assertStatus(403);
    }

    public function test_patient_gives_informed_consent_before_accessing_room(): void
    {
        $tele = $this->createTeleconsultation($this->patient, $this->telemedicineService, $this->doctor);
        $this->assertFalse($tele->patient_consent_given);

        // Submit consent
        $response = $this->actingAs($this->patientUser)->post(route('patient.telekonsultasi.consent', $tele), [
            'consent' => 1,
        ]);

        $tele->refresh();
        $this->assertTrue($tele->patient_consent_given);
        $this->assertNotNull($tele->patient_consent_at);

        // Status is scheduled, so directed to waiting room
        $response->assertRedirect(route('patient.telekonsultasi.waiting', $tele));
    }

    public function test_doctor_can_start_teleconsultation_session(): void
    {
        $tele = $this->createTeleconsultation($this->patient, $this->telemedicineService, $this->doctor);

        $response = $this->actingAs($this->doctor)->post(route('doctor.telekonsultasi.start', $tele));

        $tele->refresh();
        $this->assertEquals('doctor_ready', $tele->status);
        $this->assertNotNull($tele->started_at);
        $this->assertNotNull($tele->doctor_joined_at);
        $this->assertEquals($this->doctor->id, $tele->doctor_id);

        $response->assertRedirect(route('doctor.telekonsultasi.room', $tele));
    }

    public function test_patient_can_enter_video_room_once_doctor_is_ready(): void
    {
        $tele = $this->createTeleconsultation($this->patient, $this->telemedicineService, $this->doctor);
        $tele->update([
            'patient_consent_given' => true,
            'status' => 'doctor_ready',
        ]);

        $response = $this->actingAs($this->patientUser)->get(route('patient.telekonsultasi.room', $tele));

        $response->assertStatus(200);
        $response->assertSee('Ruang Telekonsultasi Video');
        $response->assertSee($tele->room_name);

        $tele->refresh();
        $this->assertEquals('in_progress', $tele->status);
        $this->assertNotNull($tele->patient_joined_at);
    }

    public function test_doctor_can_complete_teleconsultation_and_save_clinical_notes(): void
    {
        $tele = $this->createTeleconsultation($this->patient, $this->telemedicineService, $this->doctor);
        $tele->update([
            'status' => 'in_progress',
            'started_at' => Carbon::now()->subMinutes(12),
        ]);

        $response = $this->actingAs($this->doctor)->post(route('doctor.telekonsultasi.complete', $tele), [
            'clinical_notes' => 'Pasien flu ringan. Diberikan resep paracetamol 3x500mg dan vitamin C.',
            'disposition' => 'resep_obat',
        ]);

        $tele->refresh();
        $this->assertEquals('completed', $tele->status);
        $this->assertNotNull($tele->ended_at);
        $this->assertEquals('resep_obat', $tele->disposition);
        $this->assertStringContainsString('paracetamol', $tele->clinical_notes);
        $this->assertEquals($this->doctor->id, $tele->completed_by);

        // Linked appointment is also completed
        $this->assertEquals('completed', $tele->appointment->status);

        $response->assertRedirect(route('doctor.telekonsultasi.show', $tele));
    }

    public function test_cancelled_teleconsultation_cannot_be_joined(): void
    {
        $tele = $this->createTeleconsultation($this->patient, $this->telemedicineService, $this->doctor);
        $tele->update([
            'status' => 'cancelled',
            'patient_consent_given' => true,
        ]);

        // Patient attempt
        $responsePatient = $this->actingAs($this->patientUser)->get(route('patient.telekonsultasi.room', $tele));
        $responsePatient->assertRedirect(route('patient.dashboard'));

        // Doctor attempt
        $responseDoctor = $this->actingAs($this->doctor)->get(route('doctor.telekonsultasi.room', $tele));
        $responseDoctor->assertStatus(403);
    }

    public function test_patient_status_endpoint_returns_json_for_waiting_room_polling(): void
    {
        $tele = $this->createTeleconsultation($this->patient, $this->telemedicineService, $this->doctor);

        $response = $this->actingAs($this->patientUser)->get(route('patient.telekonsultasi.status', $tele));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'status_label',
            'can_join',
            'doctor_name',
            'doctor_ready',
            'is_cancelled',
            'is_completed',
        ]);
        $response->assertJson([
            'status' => 'scheduled',
            'can_join' => false,
            'doctor_ready' => false,
        ]);

        // Now set doctor ready
        $tele->update(['status' => 'doctor_ready']);

        $response2 = $this->actingAs($this->patientUser)->get(route('patient.telekonsultasi.status', $tele));
        $response2->assertJson([
            'status' => 'doctor_ready',
            'can_join' => true,
            'doctor_ready' => true,
        ]);
    }

    /**
     * Helper to create a teleconsultation record with an appointment.
     */
    protected function createTeleconsultation(Patient $patient, Service $service, ?User $doctor = null): Teleconsultation
    {
        $appointment = Appointment::create([
            'appointment_number' => 'RES-'.date('Ymd').'-'.rand(1000, 9999),
            'patient_id' => $patient->id,
            'service_id' => $service->id,
            'appointment_date' => Carbon::today()->format('Y-m-d'),
            'slot_time' => '10:00:00',
            'consultation_type' => 'video',
            'status' => 'booked',
        ]);

        return Teleconsultation::create([
            'appointment_id' => $appointment->id,
            'patient_id' => $patient->id,
            'service_id' => $service->id,
            'doctor_id' => $doctor?->id,
            'session_id' => Teleconsultation::generateSessionId(),
            'room_name' => Teleconsultation::generateSecureRoomName(),
            'status' => 'scheduled',
            'scheduled_at' => Carbon::now()->addMinutes(10),
            'patient_consent_given' => false,
        ]);
    }
}
