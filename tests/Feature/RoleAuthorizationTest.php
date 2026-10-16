<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_staff_portals(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('doctor.dashboard'))->assertRedirect(route('login'));
        $this->get(route('triage.index'))->assertRedirect(route('login'));
        $this->get(route('registrar.dashboard'))->assertRedirect(route('login'));
    }

    public function test_registrar_cannot_access_admin_or_doctor_or_triage_portals(): void
    {
        $registrar = User::factory()->create(['role' => 'registrar']);

        $this->actingAs($registrar)->get(route('admin.dashboard'))->assertStatus(403);
        $this->actingAs($registrar)->get(route('doctor.dashboard'))->assertStatus(403);
        $this->actingAs($registrar)->get(route('triage.index'))->assertStatus(403);

        // Allowed to registrar dashboard
        $this->actingAs($registrar)->get(route('registrar.dashboard'))->assertStatus(200);
    }

    public function test_doctor_cannot_access_admin_or_triage_or_registrar_portals(): void
    {
        $doctor = User::factory()->create(['role' => 'doctor']);

        $this->actingAs($doctor)->get(route('admin.dashboard'))->assertStatus(403);
        $this->actingAs($doctor)->get(route('triage.index'))->assertStatus(403);
        $this->actingAs($doctor)->get(route('registrar.dashboard'))->assertStatus(403);

        // Allowed to doctor dashboard
        $this->actingAs($doctor)->get(route('doctor.dashboard'))->assertStatus(200);
    }

    public function test_health_officer_cannot_access_admin_or_doctor_or_registrar_portals(): void
    {
        $officer = User::factory()->create(['role' => 'health_officer']);

        $this->actingAs($officer)->get(route('admin.dashboard'))->assertStatus(403);
        $this->actingAs($officer)->get(route('doctor.dashboard'))->assertStatus(403);
        $this->actingAs($officer)->get(route('registrar.dashboard'))->assertStatus(403);

        // Allowed to triage dashboard
        $this->actingAs($officer)->get(route('triage.index'))->assertStatus(200);
    }

    public function test_admin_has_access_to_all_portals(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertStatus(200);
        $this->actingAs($admin)->get(route('doctor.dashboard'))->assertStatus(200);
        $this->actingAs($admin)->get(route('triage.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('registrar.dashboard'))->assertStatus(200);
    }

    public function test_patient_cannot_access_any_staff_portals(): void
    {
        $patient = User::factory()->create(['role' => 'patient']);

        $this->actingAs($patient)->get(route('admin.dashboard'))->assertStatus(403);
        $this->actingAs($patient)->get(route('doctor.dashboard'))->assertStatus(403);
        $this->actingAs($patient)->get(route('triage.index'))->assertStatus(403);
        $this->actingAs($patient)->get(route('registrar.dashboard'))->assertStatus(403);
    }

    public function test_staff_or_admin_accessing_patient_dashboard_is_redirected_gracefully(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $doctor = User::factory()->create(['role' => 'doctor']);

        $this->actingAs($admin)
            ->get(route('patient.dashboard'))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($doctor)
            ->get(route('patient.dashboard'))
            ->assertRedirect(route('dashboard'));
    }
}
