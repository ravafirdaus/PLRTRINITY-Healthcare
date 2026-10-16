<?php

namespace Tests\Feature;

use App\Models\ClinicSetting;
use App\Models\Patient;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentBookingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        ClinicSetting::set('opening_time', '08:00');
        ClinicSetting::set('closing_time', '12:00');
        ClinicSetting::set('default_slot_duration_minutes', 20);
        ClinicSetting::set('max_slot_capacity', 2);
    }

    public function test_can_view_appointment_booking_page(): void
    {
        $service = Service::create([
            'name' => 'Poli Umum',
            'code' => 'UMU',
            'slot_duration_minutes' => 20,
            'max_capacity_per_slot' => 2,
            'is_active' => true,
        ]);

        $response = $this->get(route('public.appointments.create'));
        $response->assertStatus(200);
        $response->assertSee('Poli Umum');
    }

    public function test_can_check_available_slots_endpoint(): void
    {
        $service = Service::create([
            'name' => 'Poli Umum',
            'code' => 'UMU',
            'slot_duration_minutes' => 20,
            'max_capacity_per_slot' => 2,
            'is_active' => true,
        ]);

        $date = Carbon::tomorrow()->format('Y-m-d');

        $response = $this->getJson(route('public.appointments.check_slots', [
            'service_id' => $service->id,
            'date' => $date,
        ]));

        $response->assertStatus(200);
        $response->assertJsonStructure(['slots']);
        $slots = $response->json('slots');
        $this->assertNotEmpty($slots);
        $this->assertEquals('08:00:00', $slots[0]['time']);
        $this->assertEquals(2, $slots[0]['capacity']);
    }

    public function test_patient_can_book_an_appointment_successfully(): void
    {
        $service = Service::create([
            'name' => 'Poli Umum',
            'code' => 'UMU',
            'slot_duration_minutes' => 20,
            'max_capacity_per_slot' => 2,
            'is_active' => true,
        ]);

        $date = Carbon::tomorrow()->format('Y-m-d');

        $payload = [
            'nik' => '3201012304950001',
            'name' => 'Budi Pratama',
            'date_of_birth' => '1995-04-23',
            'gender' => 'L',
            'phone' => '081234567890',
            'service_id' => $service->id,
            'appointment_date' => $date,
            'slot_time' => '08:00:00',
            'notes' => 'Pemeriksaan tensi rutin',
        ];

        $response = $this->post(route('public.appointments.store'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('patients', ['nik' => '3201012304950001', 'name' => 'Budi Pratama']);
        $this->assertDatabaseHas('appointments', [
            'service_id' => $service->id,
            'slot_time' => '08:00:00',
            'status' => 'booked',
        ]);
    }

    public function test_prevents_duplicate_booking_for_same_patient_on_same_service_and_date(): void
    {
        $service = Service::create([
            'name' => 'Poli Umum',
            'code' => 'UMU',
            'slot_duration_minutes' => 20,
            'max_capacity_per_slot' => 2,
            'is_active' => true,
        ]);

        $date = Carbon::tomorrow()->format('Y-m-d');

        $payload = [
            'nik' => '3201012304950001',
            'name' => 'Budi Pratama',
            'date_of_birth' => '1995-04-23',
            'gender' => 'L',
            'phone' => '081234567890',
            'service_id' => $service->id,
            'appointment_date' => $date,
            'slot_time' => '08:00:00',
        ];

        // First booking succeeds
        $this->post(route('public.appointments.store'), $payload)->assertRedirect();

        // Second booking for same patient, same service, same date on slot 08:20
        $payload['slot_time'] = '08:20:00';
        $response2 = $this->post(route('public.appointments.store'), $payload);

        $response2->assertSessionHasErrors(['error']);
    }

    public function test_prevents_overbooking_when_slot_capacity_is_full(): void
    {
        $service = Service::create([
            'name' => 'Poli Umum',
            'code' => 'UMU',
            'slot_duration_minutes' => 20,
            'max_capacity_per_slot' => 1, // Only 1 capacity!
            'is_active' => true,
        ]);

        $date = Carbon::tomorrow()->format('Y-m-d');

        // First patient books slot 08:00
        $this->post(route('public.appointments.store'), [
            'nik' => '3201012304950001',
            'name' => 'Pasien Satu',
            'date_of_birth' => '1995-04-23',
            'gender' => 'L',
            'phone' => '081234567891',
            'service_id' => $service->id,
            'appointment_date' => $date,
            'slot_time' => '08:00:00',
        ])->assertRedirect();

        // Second different patient tries to book the SAME slot 08:00
        $response = $this->post(route('public.appointments.store'), [
            'nik' => '3201012304950002',
            'name' => 'Pasien Dua',
            'date_of_birth' => '1998-05-10',
            'gender' => 'P',
            'phone' => '081234567892',
            'service_id' => $service->id,
            'appointment_date' => $date,
            'slot_time' => '08:00:00',
        ]);

        $response->assertSessionHasErrors(['error']);
    }
}
