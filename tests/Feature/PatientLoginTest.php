<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PatientLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_login_screen_can_be_rendered(): void
    {
        $response = $this->get(route('patient.login'));

        $response->assertStatus(200);
        $response->assertSee('Login Pasien');
        $response->assertSee('Nomor HP');
    }

    public function test_patient_can_login_with_phone_and_password(): void
    {
        $user = User::create([
            'name' => 'Dewi Lestari',
            'email' => 'patient_081298765432@puskesmasflow.local',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'phone' => '081298765432',
        ]);

        $patient = Patient::create([
            'user_id' => $user->id,
            'nik' => '3201015508880002',
            'name' => 'Dewi Lestari',
            'date_of_birth' => '1988-08-15',
            'gender' => 'P',
            'phone' => '081298765432',
        ]);

        $response = $this->post(route('patient.login.store'), [
            'phone' => '081298765432',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('patient.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_patient_can_login_with_normalized_phone(): void
    {
        $user = User::create([
            'name' => 'Dewi Lestari',
            'email' => 'patient_081298765432@puskesmasflow.local',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'phone' => '081298765432',
        ]);

        Patient::create([
            'user_id' => $user->id,
            'nik' => '3201015508880002',
            'name' => 'Dewi Lestari',
            'date_of_birth' => '1988-08-15',
            'gender' => 'P',
            'phone' => '081298765432',
        ]);

        // Submit as +62 812 9876 5432
        $response = $this->post(route('patient.login.store'), [
            'phone' => '+6281298765432',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('patient.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_patient_login_fails_with_invalid_password(): void
    {
        $user = User::create([
            'name' => 'Dewi Lestari',
            'email' => 'patient_081298765432@puskesmasflow.local',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'phone' => '081298765432',
        ]);

        Patient::create([
            'user_id' => $user->id,
            'nik' => '3201015508880002',
            'name' => 'Dewi Lestari',
            'date_of_birth' => '1988-08-15',
            'gender' => 'P',
            'phone' => '081298765432',
        ]);

        $response = $this->post(route('patient.login.store'), [
            'phone' => '081298765432',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors(['phone']);
        $this->assertGuest();
    }

    public function test_patient_can_logout(): void
    {
        $user = User::create([
            'name' => 'Dewi Lestari',
            'email' => 'patient_081298765432@puskesmasflow.local',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'phone' => '081298765432',
        ]);

        $response = $this->actingAs($user)->post(route('patient.logout'));

        $response->assertRedirect(route('patient.login'));
        $this->assertGuest();
    }
}
