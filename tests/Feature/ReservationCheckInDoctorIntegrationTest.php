<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\ClinicSetting;
use App\Models\Patient;
use App\Models\QueueEntry;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationCheckInDoctorIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $doctorGigi;

    protected User $doctorUmum;

    protected Service $poliGigi;

    protected Service $poliUmum;

    protected function setUp(): void
    {
        parent::setUp();

        ClinicSetting::set('late_tolerance_minutes', 30);
        ClinicSetting::set('default_slot_duration_minutes', 20);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'Administrator',
            'email' => 'admin@puskesmasflow.local',
        ]);

        $this->doctorGigi = User::factory()->create([
            'role' => 'doctor',
            'name' => 'drg. Maya Ananda',
            'email' => 'dokter.gigi@puskesmasflow.local',
        ]);

        $this->doctorUmum = User::factory()->create([
            'role' => 'doctor',
            'name' => 'dr. Dian Pratama',
            'email' => 'dokter.umum@puskesmasflow.local',
        ]);

        $this->poliGigi = Service::create([
            'name' => 'Poli Gigi & Mulut',
            'code' => 'GIG',
            'slot_duration_minutes' => 25,
            'max_capacity_per_slot' => 3,
            'is_active' => true,
        ]);

        $this->poliUmum = Service::create([
            'name' => 'Poli Umum',
            'code' => 'UMU',
            'slot_duration_minutes' => 20,
            'max_capacity_per_slot' => 4,
            'is_active' => true,
        ]);
    }

    /**
     * Skenario 1 — Check-in reservasi berhasil
     * Reservasi valid untuk Poli Gigi & Mulut.
     * Admin melakukan check-in.
     * Status reservasi berubah sesuai alur yang berlaku.
     * Pasien muncul tepat satu kali pada antrean dokter poli yang sesuai.
     */
    public function test_scenario_1_successful_check_in_appears_on_doctor_queue(): void
    {
        $patient = Patient::create([
            'nik' => '3201012304950001',
            'name' => 'Budi Hartono',
            'date_of_birth' => '1992-05-10',
            'gender' => 'L',
            'phone' => '081234567891',
        ]);

        $today = Carbon::today()->format('Y-m-d');
        $appointment = Appointment::create([
            'appointment_number' => 'RES-20261014-GIG1',
            'patient_id' => $patient->id,
            'service_id' => $this->poliGigi->id,
            'appointment_date' => $today,
            'slot_time' => Carbon::now()->format('H:i:00'),
            'status' => 'booked',
        ]);

        // Admin checks in the patient
        $response = $this->actingAs($this->admin)
            ->post(route('registrar.check_in', $appointment->id));

        $response->assertRedirect();

        // Status reservasi berubah menjadi checked_in
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'checked_in',
        ]);

        // Antrean dibuat dengan format nomor tiket yang benar dan status waiting_call
        $this->assertDatabaseHas('queue_entries', [
            'appointment_id' => $appointment->id,
            'service_id' => $this->poliGigi->id,
            'patient_id' => $patient->id,
            'queue_code' => 'GIG-001',
            'status' => 'waiting_call',
        ]);

        // Pasien muncul tepat satu kali pada antrean dokter Poli Gigi & Mulut
        $doctorResponse = $this->actingAs($this->doctorGigi)
            ->get(route('doctor.dashboard', ['service_id' => $this->poliGigi->id]));

        $doctorResponse->assertStatus(200);
        $doctorResponse->assertSee('GIG-001');
        $doctorResponse->assertSee('Budi Hartono');
        $doctorResponse->assertSee('Antrean Menunggu Dipanggil (1)');
    }

    /**
     * Skenario 2 — Check-in berulang (Idempotency)
     * Admin mengirim request check-in yang sama lebih dari sekali.
     * Sistem tidak membuat nomor atau record antrean duplikat.
     */
    public function test_scenario_2_repeated_check_in_is_idempotent_no_duplicates(): void
    {
        $patient = Patient::create([
            'nik' => '3201012304950002',
            'name' => 'Siti Aminah',
            'date_of_birth' => '1994-08-12',
            'gender' => 'P',
            'phone' => '081234567892',
        ]);

        $today = Carbon::today()->format('Y-m-d');
        $appointment = Appointment::create([
            'appointment_number' => 'RES-20261014-GIG2',
            'patient_id' => $patient->id,
            'service_id' => $this->poliGigi->id,
            'appointment_date' => $today,
            'slot_time' => Carbon::now()->format('H:i:00'),
            'status' => 'booked',
        ]);

        // First check-in
        $response1 = $this->actingAs($this->admin)
            ->post(route('registrar.check_in', $appointment->id));
        $response1->assertRedirect();

        $firstQueue = QueueEntry::where('appointment_id', $appointment->id)->first();
        $this->assertNotNull($firstQueue);
        $this->assertEquals('GIG-001', $firstQueue->queue_code);

        // Second check-in (repeated request)
        $response2 = $this->actingAs($this->admin)
            ->post(route('registrar.check_in', $appointment->id));
        $response2->assertRedirect(route('registrar.ticket', $firstQueue->id));

        // Third check-in (another repeated request)
        $response3 = $this->actingAs($this->admin)
            ->post(route('registrar.check_in', $appointment->id));
        $response3->assertRedirect(route('registrar.ticket', $firstQueue->id));

        // Queue entry count must remain exactly 1 (no duplicate)
        $this->assertEquals(1, QueueEntry::where('appointment_id', $appointment->id)->count());
        $this->assertEquals(1, QueueEntry::where('service_id', $this->poliGigi->id)->whereDate('queue_date', $today)->count());
    }

    /**
     * Skenario 3 — Poli berbeda
     * Pasien terdaftar pada poli lain (Poli Umum).
     * Pasien tidak muncul pada antrean Poli Gigi & Mulut, tetapi muncul pada poli yang benar.
     */
    public function test_scenario_3_different_services_isolated(): void
    {
        $patientUmum = Patient::create([
            'nik' => '3201012304950003',
            'name' => 'Agus Priyono',
            'date_of_birth' => '1988-12-01',
            'gender' => 'L',
            'phone' => '081234567893',
        ]);

        $today = Carbon::today()->format('Y-m-d');
        $appointmentUmum = Appointment::create([
            'appointment_number' => 'RES-20261014-UMU1',
            'patient_id' => $patientUmum->id,
            'service_id' => $this->poliUmum->id,
            'appointment_date' => $today,
            'slot_time' => Carbon::now()->format('H:i:00'),
            'status' => 'booked',
        ]);

        $checkInResponse = $this->actingAs($this->admin)->post(route('registrar.check_in', $appointmentUmum->id));
        $checkInResponse->assertRedirect();
        $this->flushSession();

        // Check Doctor Poli Gigi dashboard: should NOT see Agus Priyono or UMU-001
        $doctorGigiResponse = $this->actingAs($this->doctorGigi)
            ->get(route('doctor.dashboard', ['service_id' => $this->poliGigi->id]));

        $doctorGigiResponse->assertStatus(200);
        $doctorGigiResponse->assertDontSee('Agus Priyono');
        $doctorGigiResponse->assertDontSee('UMU-001');
        $doctorGigiResponse->assertSee('Antrean Menunggu Dipanggil (0)');

        // Check Doctor Poli Umum dashboard: MUST see Agus Priyono and UMU-001
        $doctorUmumResponse = $this->actingAs($this->doctorUmum)
            ->get(route('doctor.dashboard', ['service_id' => $this->poliUmum->id]));

        $doctorUmumResponse->assertStatus(200);
        $doctorUmumResponse->assertSee('Agus Priyono');
        $doctorUmumResponse->assertSee('UMU-001');
        $doctorUmumResponse->assertSee('Antrean Menunggu Dipanggil (1)');
    }

    /**
     * Skenario 4 — Reservasi dibatalkan
     * Reservasi berstatus dibatalkan.
     * Sistem tidak memasukkan reservasi tersebut ke antrean aktif secara tidak semestinya.
     */
    public function test_scenario_4_cancelled_reservation_cannot_be_checked_in(): void
    {
        $patient = Patient::create([
            'nik' => '3201012304950004',
            'name' => 'Ratna Sari',
            'date_of_birth' => '1996-03-20',
            'gender' => 'P',
            'phone' => '081234567894',
        ]);

        $today = Carbon::today()->format('Y-m-d');
        $appointment = Appointment::create([
            'appointment_number' => 'RES-20261014-CANCELLED',
            'patient_id' => $patient->id,
            'service_id' => $this->poliGigi->id,
            'appointment_date' => $today,
            'slot_time' => Carbon::now()->format('H:i:00'),
            'status' => 'cancelled',
        ]);

        // Attempt check-in
        $response = $this->actingAs($this->admin)
            ->post(route('registrar.check_in', $appointment->id));

        $response->assertSessionHasErrors(['error']);

        // No queue entry created
        $this->assertDatabaseMissing('queue_entries', [
            'appointment_id' => $appointment->id,
        ]);

        // Doctor queue must remain 0
        $doctorResponse = $this->actingAs($this->doctorGigi)
            ->get(route('doctor.dashboard', ['service_id' => $this->poliGigi->id]));

        $doctorResponse->assertSee('Antrean Menunggu Dipanggil (0)');
        $doctorResponse->assertDontSee('Ratna Sari');
    }

    /**
     * Skenario 5 — Alur pelayanan dokter
     * Pasien yang sudah berada di antrean dapat dipanggil sesuai mekanisme yang tersedia.
     * Status pasien berpindah sesuai alur pelayanan yang sudah ada.
     * Pasien yang sudah selesai dilayani tidak muncul lagi sebagai antrean menunggu dipanggil.
     */
    public function test_scenario_5_full_doctor_consultation_lifecycle(): void
    {
        $patient = Patient::create([
            'nik' => '3201012304950005',
            'name' => 'Eko Prasetyo',
            'date_of_birth' => '1985-07-15',
            'gender' => 'L',
            'phone' => '081234567895',
        ]);

        $today = Carbon::today()->format('Y-m-d');
        $appointment = Appointment::create([
            'appointment_number' => 'RES-20261014-GIG5',
            'patient_id' => $patient->id,
            'service_id' => $this->poliGigi->id,
            'appointment_date' => $today,
            'slot_time' => Carbon::now()->format('H:i:00'),
            'status' => 'booked',
        ]);

        // 1. Check in
        $this->actingAs($this->admin)->post(route('registrar.check_in', $appointment->id));
        $queueEntry = QueueEntry::where('appointment_id', $appointment->id)->first();
        $this->assertEquals('waiting_call', $queueEntry->status);

        // 2. Doctor calls patient
        $callResponse = $this->actingAs($this->doctorGigi)
            ->post(route('doctor.call', $queueEntry->id), [
                'counter' => 'Ruang Poli Gigi',
            ]);
        $callResponse->assertRedirect();

        $queueEntry->refresh();
        $this->assertEquals('called', $queueEntry->status);

        // 3. Doctor starts consultation
        $startResponse = $this->actingAs($this->doctorGigi)
            ->post(route('doctor.start', $queueEntry->id));
        $startResponse->assertRedirect();

        $queueEntry->refresh();
        $this->assertEquals('in_consultation', $queueEntry->status);

        // 4. Doctor completes consultation
        $completeResponse = $this->actingAs($this->doctorGigi)
            ->post(route('doctor.complete', $queueEntry->id), [
                'clinical_notes' => 'Pembersihan karang gigi selesai, edukasi cara menyikat gigi yang benar.',
                'disposition' => 'pulang',
            ]);
        $completeResponse->assertRedirect();

        $queueEntry->refresh();
        $appointment->refresh();
        $this->assertEquals('completed', $queueEntry->status);
        $this->assertEquals('completed', $appointment->status);

        // Pasien tidak muncul lagi sebagai antrean menunggu dipanggil
        $doctorResponse = $this->actingAs($this->doctorGigi)
            ->get(route('doctor.dashboard', ['service_id' => $this->poliGigi->id]));

        $doctorResponse->assertSee('Antrean Menunggu Dipanggil (0)');
        $doctorResponse->assertSee('Selesai Pelayanan Hari Ini (1)');
        $doctorResponse->assertSee('Eko Prasetyo');
    }

    /**
     * Skenario 6 — Isolasi data tanggal pelayanan
     * Periksa agar perbaikan tidak menyebabkan antrean dari poli atau tanggal lain tercampur.
     */
    public function test_scenario_6_date_isolation(): void
    {
        $patient = Patient::create([
            'nik' => '3201012304950006',
            'name' => 'Hendra Setiawan',
            'date_of_birth' => '1990-11-20',
            'gender' => 'L',
            'phone' => '081234567896',
        ]);

        $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        $appointmentTomorrow = Appointment::create([
            'appointment_number' => 'RES-TOMORROW-001',
            'patient_id' => $patient->id,
            'service_id' => $this->poliGigi->id,
            'appointment_date' => $tomorrow,
            'slot_time' => '10:00:00',
            'status' => 'booked',
        ]);

        // Attempting check-in today for tomorrow's appointment must fail
        $response = $this->actingAs($this->admin)
            ->post(route('registrar.check_in', $appointmentTomorrow->id));

        $response->assertSessionHasErrors(['error']);

        // Doctor dashboard today does not see tomorrow's patient
        $doctorResponse = $this->actingAs($this->doctorGigi)
            ->get(route('doctor.dashboard', ['service_id' => $this->poliGigi->id]));

        $doctorResponse->assertDontSee('Hendra Setiawan');
        $doctorResponse->assertSee('Antrean Menunggu Dipanggil (0)');
    }
}
