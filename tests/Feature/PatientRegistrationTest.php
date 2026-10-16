<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('patient.register'));

        $response->assertStatus(200);
        $response->assertSee('Pendaftaran Akun');
        $response->assertSee('Data Identitas Pasien');
        $response->assertSee('Transparansi Subsidi &amp; Biaya Layanan', false);
        $response->assertSee('Pratinjau Langsung e-KIP');
        $response->assertSee('Keamanan Akun Pasien');
    }

    public function test_new_patient_can_register(): void
    {
        $response = $this->post(route('patient.register.store'), [
            'nik' => '3201012304959999',
            'name' => 'Siti Nurhaliza',
            'phone' => '081299998888',
            'date_of_birth' => '1998-05-20',
            'gender' => 'female',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'address' => 'Jl. Mawar No. 10',
            'bpjs_number' => '0009876543210',
            'consent' => '1',
        ]);

        $response->assertRedirect(route('patient.dashboard'));
        $this->assertAuthenticated();

        // Check user creation
        $this->assertDatabaseHas('users', [
            'name' => 'Siti Nurhaliza',
            'role' => 'patient',
            'phone' => '081299998888',
        ]);

        // Check patient profile creation
        $this->assertDatabaseHas('patients', [
            'nik' => '3201012304959999',
            'name' => 'Siti Nurhaliza',
            'gender' => 'P',
            'phone' => '081299998888',
        ]);
    }

    public function test_registration_requires_consent(): void
    {
        $response = $this->post(route('patient.register.store'), [
            'nik' => '3201012304959999',
            'name' => 'Siti Nurhaliza',
            'phone' => '081299998888',
            'date_of_birth' => '1998-05-20',
            'gender' => 'female',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            // consent omitted
        ]);

        $response->assertSessionHasErrors(['consent']);
        $this->assertGuest();
    }

    public function test_registration_validates_nik_length(): void
    {
        // 15 digits (invalid)
        $response = $this->post(route('patient.register.store'), [
            'nik' => '320101230495999',
            'name' => 'Test User',
            'phone' => '081299998888',
            'date_of_birth' => '1998-05-20',
            'gender' => 'male',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'consent' => '1',
        ]);

        $response->assertSessionHasErrors(['nik']);
    }

    public function test_registration_prevents_duplicate_nik(): void
    {
        // Create initial patient
        Patient::create([
            'nik' => '3201012304950001',
            'name' => 'Existing Patient',
            'date_of_birth' => '1990-01-01',
            'gender' => 'L',
            'phone' => '081211112222',
        ]);

        // Attempt register with same NIK
        $response = $this->post(route('patient.register.store'), [
            'nik' => '3201012304950001',
            'name' => 'Duplicate Person',
            'phone' => '081233334444',
            'date_of_birth' => '1995-02-02',
            'gender' => 'male',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'consent' => '1',
        ]);

        $response->assertSessionHasErrors(['nik']);
        $this->assertGuest();
    }

    public function test_phone_normalization_on_registration(): void
    {
        // Phone submitted as +62 812-3456-7899
        $response = $this->post(route('patient.register.store'), [
            'nik' => '3201012304958888',
            'name' => 'Budi Normalized',
            'phone' => '+6281234567899',
            'date_of_birth' => '1992-03-15',
            'gender' => 'male',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'consent' => '1',
        ]);

        $response->assertRedirect(route('patient.dashboard'));

        $this->assertDatabaseHas('patients', [
            'phone' => '081234567899',
        ]);
        $this->assertDatabaseHas('users', [
            'phone' => '081234567899',
        ]);
    }

    public function test_patient_dashboard_renders_digital_card_with_required_fields(): void
    {
        $response = $this->post(route('patient.register.store'), [
            'nik' => '3276012304951111',
            'name' => 'Ahmad Kartu Digital',
            'phone' => '081255556666',
            'date_of_birth' => '1995-07-21',
            'gender' => 'male',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'address' => 'Pancoran Mas, Kota Depok',
            'bpjs_number' => '0001234567890',
            'consent' => '1',
        ]);

        $response->assertRedirect(route('patient.dashboard'));

        $dashboardResponse = $this->get(route('patient.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Puskesmas Kota Depok');
        $dashboardResponse->assertSee('Kartu Pasien Digital');
        $dashboardResponse->assertSee('Ahmad Kartu Digital');
        $dashboardResponse->assertSee('Kunci Unik Pasien');
        $dashboardResponse->assertSee('Subsidi Penuh (Bebas Biaya Rp 0)');
        $dashboardResponse->assertSee('Pancoran Mas, Kota Depok');
    }

    public function test_patient_can_access_dedicated_card_print_view(): void
    {
        $response = $this->post(route('patient.register.store'), [
            'nik' => '3276012304952222',
            'name' => 'Budi Cetak Kartu',
            'phone' => '081277778888',
            'date_of_birth' => '1992-04-10',
            'gender' => 'male',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'address' => 'Sukmajaya, Kota Depok',
            'consent' => '1',
        ]);

        $response->assertRedirect(route('patient.dashboard'));

        $printResponse = $this->get(route('patient.card.print'));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('Puskesmas Kota Depok');
        $printResponse->assertSee('Budi Cetak Kartu');
        $printResponse->assertSee('Download PDF');
        $printResponse->assertSee('Mandiri / Reguler');
    }

    public function test_patient_reservation_automatically_reflects_registered_ekip_tier_without_manual_selection(): void
    {
        // Register patient as Mandiri
        $response = $this->post(route('patient.register.store'), [
            'nik' => '3276012304953333',
            'name' => 'Citra Mandiri',
            'phone' => '081233334444',
            'date_of_birth' => '1999-01-15',
            'gender' => 'female',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'patient_tier' => 'mandiri',
            'address' => 'Cinere, Kota Depok',
            'consent' => '1',
        ]);

        $response->assertRedirect(route('patient.dashboard'));

        // Visit reservation create page
        $resPage = $this->get(route('patient.reservasi.create'));
        $resPage->assertStatus(200);
        $resPage->assertSee('Otomatis dari Kartu e-KIP');
        $resPage->assertSee('Mandiri / Reguler');
        $resPage->assertSee('Total Estimasi Biaya Kunjungan:');
        $resPage->assertSee('Rp 25.000');
        // Ensure manual radio choice is gone
        $resPage->assertDontSee('name="patient_tier"', false);
    }
}
