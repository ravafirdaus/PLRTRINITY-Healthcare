<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Service;
use App\Models\StaffSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DoctorScheduleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $doctorUser;

    protected User $patientUser;

    protected Patient $patient;

    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'Administrator Puskesmas',
            'email' => 'admin@puskesmasflow.local',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $this->doctorUser = User::create([
            'name' => 'dr. Dian Pratama',
            'email' => 'dokter@puskesmasflow.local',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'phone' => '081234567893',
        ]);

        $this->patientUser = User::create([
            'name' => 'Rava Gunawan',
            'email' => 'rava@puskesmasflow.local',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'phone' => '081298765431',
        ]);

        $this->patient = Patient::create([
            'user_id' => $this->patientUser->id,
            'nik' => '3201012304950001',
            'name' => 'Rava Gunawan',
            'date_of_birth' => '1995-04-23',
            'gender' => 'L',
            'phone' => '081298765431',
            'address' => 'Jl. Margonda No. 12',
        ]);

        $this->service = Service::create([
            'name' => 'Poli Umum',
            'code' => 'UMU',
            'slot_duration_minutes' => 20,
            'max_capacity_per_slot' => 4,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_view_doctor_schedules(): void
    {
        StaffSchedule::create([
            'user_id' => $this->doctorUser->id,
            'service_id' => $this->service->id,
            'day_of_week' => 1,
            'start_time' => '08:00',
            'end_time' => '14:00',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Jadwal Praktik Dokter');
        $response->assertSee('dr. Dian Pratama');
        $response->assertSee('Poli Umum');
    }

    public function test_admin_can_create_doctor_schedule(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.schedules.store'), [
            'user_id' => $this->doctorUser->id,
            'service_id' => $this->service->id,
            'day_of_week' => 2,
            'start_time' => '08:30',
            'end_time' => '13:30',
            'is_active' => 1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('staff_schedules', [
            'user_id' => $this->doctorUser->id,
            'service_id' => $this->service->id,
            'day_of_week' => 2,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_update_doctor_schedule(): void
    {
        $schedule = StaffSchedule::create([
            'user_id' => $this->doctorUser->id,
            'service_id' => $this->service->id,
            'day_of_week' => 3,
            'start_time' => '08:00',
            'end_time' => '12:00',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->adminUser)->put(route('admin.schedules.update', $schedule), [
            'user_id' => $this->doctorUser->id,
            'service_id' => $this->service->id,
            'day_of_week' => 3,
            'start_time' => '09:00',
            'end_time' => '15:00',
            'is_active' => 1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $fresh = $schedule->fresh();
        $this->assertStringStartsWith('09:00', $fresh->start_time);
        $this->assertStringStartsWith('15:00', $fresh->end_time);
    }

    public function test_admin_can_delete_doctor_schedule(): void
    {
        $schedule = StaffSchedule::create([
            'user_id' => $this->doctorUser->id,
            'service_id' => $this->service->id,
            'day_of_week' => 4,
            'start_time' => '08:00',
            'end_time' => '12:00',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->adminUser)->delete(route('admin.schedules.destroy', $schedule));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('staff_schedules', [
            'id' => $schedule->id,
        ]);
    }

    public function test_patient_can_see_available_doctors_when_checking_slots(): void
    {
        // Schedule doctor on Monday (1)
        StaffSchedule::create([
            'user_id' => $this->doctorUser->id,
            'service_id' => $this->service->id,
            'day_of_week' => 1,
            'start_time' => '08:00',
            'end_time' => '14:00',
            'is_active' => true,
        ]);

        // Next Monday
        $nextMonday = Carbon::now()->next(Carbon::MONDAY)->format('Y-m-d');

        $response = $this->actingAs($this->patientUser)->getJson(route('patient.reservasi.slots', [
            'service_id' => $this->service->id,
            'date' => $nextMonday,
        ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'slots',
            'doctors' => [
                '*' => [
                    'doctor_id',
                    'doctor_name',
                    'service_name',
                    'formatted_schedule',
                ],
            ],
        ]);

        $response->assertJsonFragment([
            'doctor_id' => $this->doctorUser->id,
            'doctor_name' => 'dr. Dian Pratama',
        ]);
    }

    public function test_patient_reservation_stores_assigned_doctor(): void
    {
        $nextMonday = Carbon::now()->next(Carbon::MONDAY)->format('Y-m-d');

        StaffSchedule::create([
            'user_id' => $this->doctorUser->id,
            'service_id' => $this->service->id,
            'day_of_week' => 1,
            'start_time' => '08:00',
            'end_time' => '14:00',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->patientUser)->post(route('patient.reservasi.store'), [
            'service_id' => $this->service->id,
            'doctor_id' => $this->doctorUser->id,
            'appointment_date' => $nextMonday,
            'slot_time' => '08:40:00',
            'notes' => 'Pemeriksaan rutin mingguan.',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $appointment = Appointment::where('patient_id', $this->patient->id)->latest('id')->first();
        $this->assertNotNull($appointment);
        $this->assertEquals($this->doctorUser->id, $appointment->doctor_id);
        $this->assertEquals('dr. Dian Pratama', $appointment->doctor->name);
    }
}
