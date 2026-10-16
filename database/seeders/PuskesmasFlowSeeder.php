<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\ClinicSetting;
use App\Models\Patient;
use App\Models\QueueEntry;
use App\Models\QueueStatusHistory;
use App\Models\Service;
use App\Models\StaffSchedule;
use App\Models\TriageAssessment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PuskesmasFlowSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Clinic Settings
        $settings = [
            'opening_time' => ['value' => '08:00', 'desc' => 'Jam Buka Pelayanan Puskesmas'],
            'closing_time' => ['value' => '15:00', 'desc' => 'Jam Tutup Pelayanan Puskesmas'],
            'default_slot_duration_minutes' => ['value' => '20', 'desc' => 'Estimasi Waktu Konsultasi Standar (menit)'],
            'max_slot_capacity' => ['value' => '4', 'desc' => 'Kapasitas Pasien per Slot Waktu'],
            'late_tolerance_minutes' => ['value' => '15', 'desc' => 'Toleransi Keterlambatan Check-in (menit)'],
        ];

        foreach ($settings as $key => $item) {
            ClinicSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $item['value'], 'description' => $item['desc']]
            );
        }

        // 2. Demo Users for all 4 roles
        $admin = User::updateOrCreate(
            ['email' => 'admin@puskesmasflow.local'],
            [
                'name' => 'Administrator Puskesmas',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '081234567890',
            ]
        );

        $registrar = User::updateOrCreate(
            ['email' => 'pendaftaran@puskesmasflow.local'],
            [
                'name' => 'Siti Rahma (Petugas Loket)',
                'password' => Hash::make('daftar123'),
                'role' => 'registrar',
                'phone' => '081234567891',
            ]
        );

        $healthOfficer = User::updateOrCreate(
            ['email' => 'triase@puskesmasflow.local'],
            [
                'name' => 'Ns. Budi Santoso, S.Kep (Petugas Triase)',
                'password' => Hash::make('triase123'),
                'role' => 'health_officer',
                'phone' => '081234567892',
            ]
        );

        $doctorUmum = User::updateOrCreate(
            ['email' => 'dokter@puskesmasflow.local'],
            [
                'name' => 'dr. Dian Pratama (Dokter Poli Umum)',
                'password' => Hash::make('dokter123'),
                'role' => 'doctor',
                'phone' => '081234567893',
            ]
        );

        $doctorGigi = User::updateOrCreate(
            ['email' => 'dokter.gigi@puskesmasflow.local'],
            [
                'name' => 'drg. Maya Ananda (Dokter Poli Gigi)',
                'password' => Hash::make('dokter123'),
                'role' => 'doctor',
                'phone' => '081234567894',
            ]
        );

        // 3. Services (Poli)
        $poliUmum = Service::updateOrCreate(
            ['code' => 'UMU'],
            [
                'name' => 'Poli Umum',
                'description' => 'Pelayanan medis dasar umum, pemeriksaan keluhan harian dan rujukan.',
                'slot_duration_minutes' => 20,
                'max_capacity_per_slot' => 4,
                'is_active' => true,
                'supports_teleconsultation' => true,
            ]
        );

        $poliGigi = Service::updateOrCreate(
            ['code' => 'GIG'],
            [
                'name' => 'Poli Gigi & Mulut',
                'description' => 'Pemeriksaan kesehatan gigi, penambalan, pencabutan dan konsultasi.',
                'slot_duration_minutes' => 25,
                'max_capacity_per_slot' => 3,
                'is_active' => true,
                'supports_teleconsultation' => false,
            ]
        );

        $poliKIA = Service::updateOrCreate(
            ['code' => 'KIA'],
            [
                'name' => 'Poli KIA & KB',
                'description' => 'Kesehatan ibu hamil, balita, imunisasi, dan pelayanan keluarga berencana.',
                'slot_duration_minutes' => 20,
                'max_capacity_per_slot' => 4,
                'is_active' => true,
                'supports_teleconsultation' => true,
            ]
        );

        $poliLansia = Service::updateOrCreate(
            ['code' => 'LAN'],
            [
                'name' => 'Poli Lansia & Disabilitas',
                'description' => 'Pelayanan ramah lansia, penyakit degeneratif kronis, dan lansia resti.',
                'slot_duration_minutes' => 20,
                'max_capacity_per_slot' => 3,
                'is_active' => true,
            ]
        );

        // 4. Staff Schedules
        foreach ([1, 2, 3, 4, 5, 6] as $day) {
            StaffSchedule::updateOrCreate(
                ['user_id' => $doctorUmum->id, 'service_id' => $poliUmum->id, 'day_of_week' => $day],
                ['start_time' => '08:00', 'end_time' => '14:00', 'is_active' => true]
            );
            StaffSchedule::updateOrCreate(
                ['user_id' => $doctorGigi->id, 'service_id' => $poliGigi->id, 'day_of_week' => $day],
                ['start_time' => '08:30', 'end_time' => '13:30', 'is_active' => true]
            );
        }

        // 5. Demo Patients (Data Minimization: NIK, Nama, Tanggal Lahir, JK, Kontak)
        $patientsData = [
            [
                'nik' => '3201012304950001',
                'name' => 'Ahmad Fauzi',
                'date_of_birth' => '1995-04-23',
                'gender' => 'L',
                'phone' => '081298765431',
                'address' => 'Jl. Merdeka No. 12, Kel. Sukamaju',
                'bpjs_number' => '0001234567891',
            ],
            [
                'nik' => '3201015508880002',
                'name' => 'Dewi Lestari',
                'date_of_birth' => '1988-08-15',
                'gender' => 'P',
                'phone' => '081298765432',
                'address' => 'Jl. Melati No. 4, Kel. Sukamaju',
                'bpjs_number' => '0001234567892',
            ],
            [
                'nik' => '3201011002010003',
                'name' => 'Bambang Sudarsono',
                'date_of_birth' => '1961-02-10',
                'gender' => 'L',
                'phone' => '081298765433',
                'address' => 'Jl. Kenanga No. 8, Kel. Harapan',
                'bpjs_number' => '0001234567893',
            ],
            [
                'nik' => '3201016209990004',
                'name' => 'Nur Aini',
                'date_of_birth' => '1999-09-22',
                'gender' => 'P',
                'phone' => '081298765434',
                'address' => 'Jl. Mawar No. 19, Kel. Harapan',
                'bpjs_number' => '0001234567894',
            ],
            [
                'nik' => '3201011512750005',
                'name' => 'Hendro Wijaya',
                'date_of_birth' => '1975-12-15',
                'gender' => 'L',
                'phone' => '081298765435',
                'address' => 'Jl. Flamboyan No. 2, Kel. Makmur',
                'bpjs_number' => '0001234567895',
            ],
        ];

        $patients = [];
        foreach ($patientsData as $pData) {
            $patients[] = Patient::updateOrCreate(['nik' => $pData['nik']], $pData);
        }

        // Demo Patient Account for Ahmad Fauzi
        $patientUser = User::updateOrCreate(
            ['email' => 'patient_081298765431@puskesmasflow.local'],
            [
                'name' => 'Ahmad Fauzi',
                'password' => Hash::make('password'),
                'role' => 'patient',
                'phone' => '081298765431',
            ]
        );
        $patients[0]->update(['user_id' => $patientUser->id]);

        $today = Carbon::today();

        // 6. Demo Appointments for Today
        $appt1 = Appointment::updateOrCreate(
            ['appointment_number' => 'RES-'.$today->format('Ymd').'-0001'],
            [
                'patient_id' => $patients[0]->id,
                'service_id' => $poliUmum->id,
                'appointment_date' => $today->format('Y-m-d'),
                'slot_time' => '08:00:00',
                'status' => 'checked_in',
                'notes' => 'Demam dan pusing 3 hari.',
                'checked_in_at' => $today->copy()->setTime(7, 50),
            ]
        );

        $appt2 = Appointment::updateOrCreate(
            ['appointment_number' => 'RES-'.$today->format('Ymd').'-0002'],
            [
                'patient_id' => $patients[1]->id,
                'service_id' => $poliUmum->id,
                'appointment_date' => $today->format('Y-m-d'),
                'slot_time' => '08:20:00',
                'status' => 'booked',
                'notes' => 'Konsultasi batuk pilek.',
            ]
        );

        $appt3 = Appointment::updateOrCreate(
            ['appointment_number' => 'RES-'.$today->format('Ymd').'-0003'],
            [
                'patient_id' => $patients[2]->id,
                'service_id' => $poliLansia->id,
                'appointment_date' => $today->format('Y-m-d'),
                'slot_time' => '09:00:00',
                'status' => 'booked',
                'notes' => 'Pemeriksaan rutin tensi dan gula darah.',
            ]
        );

        // 7. Demo Queue Entries & Triage (Simulate active queue state)
        // Patient 1: Already checked in and Triaged -> waiting_call
        $q1 = QueueEntry::updateOrCreate(
            ['queue_code' => 'UMU-001', 'service_id' => $poliUmum->id],
            [
                'queue_date' => $today->format('Y-m-d'),
                'patient_id' => $patients[0]->id,
                'appointment_id' => $appt1->id,
                'registration_type' => 'reservation',
                'priority_level' => 3, // Hijau / Normal
                'status' => 'waiting_call',
                'estimated_call_time' => $today->copy()->setTime(8, 20),
            ]
        );

        TriageAssessment::updateOrCreate(
            ['queue_entry_id' => $q1->id],
            [
                'assessed_by' => $healthOfficer->id,
                'category' => 'hijau',
                'systolic_bp' => 120,
                'diastolic_bp' => 80,
                'heart_rate' => 82,
                'respiratory_rate' => 18,
                'temperature' => 37.8,
                'oxygen_saturation' => 98,
                'consciousness_level' => 'compos_mentis',
                'chief_complaint' => 'Demam dan sakit kepala sejak 3 hari yang lalu.',
                'observations' => 'Keadaan umum tampak sedang, tidak ada tanda dehidrasi berat.',
                'is_immediate_response_triggered' => false,
                'assessed_at' => $today->copy()->setTime(8, 0),
            ]
        );

        // Patient 4: Walk-in Emergency case (Merah) -> Priority 1
        $q2 = QueueEntry::updateOrCreate(
            ['queue_code' => 'EMG-001', 'service_id' => $poliUmum->id],
            [
                'queue_date' => $today->format('Y-m-d'),
                'patient_id' => $patients[3]->id,
                'appointment_id' => null,
                'registration_type' => 'emergency',
                'priority_level' => 1, // Emergency Merah!
                'status' => 'waiting_call',
                'estimated_call_time' => $today->copy()->setTime(8, 5),
            ]
        );

        TriageAssessment::updateOrCreate(
            ['queue_entry_id' => $q2->id],
            [
                'assessed_by' => $healthOfficer->id,
                'category' => 'merah',
                'systolic_bp' => 140,
                'diastolic_bp' => 90,
                'heart_rate' => 118,
                'respiratory_rate' => 28,
                'temperature' => 38.5,
                'oxygen_saturation' => 91,
                'consciousness_level' => 'somnolen',
                'chief_complaint' => 'Sesak napas berat mendadak dan penurunan kesadaran.',
                'observations' => 'Retraksi dinding dada (+), sianosis ringan, respons melambat.',
                'is_immediate_response_triggered' => true,
                'assessed_at' => $today->copy()->setTime(8, 5),
            ]
        );

        // Patient 5: Walk-in waiting for triage
        $q3 = QueueEntry::updateOrCreate(
            ['queue_code' => 'UMU-002', 'service_id' => $poliUmum->id],
            [
                'queue_date' => $today->format('Y-m-d'),
                'patient_id' => $patients[4]->id,
                'appointment_id' => null,
                'registration_type' => 'walk_in',
                'priority_level' => 3,
                'status' => 'waiting_triage',
            ]
        );

        // Status histories
        QueueStatusHistory::create([
            'queue_entry_id' => $q1->id,
            'changed_by' => $registrar->id,
            'from_status' => null,
            'to_status' => 'waiting_triage',
            'reason' => 'Check-in awal loket',
            'created_at' => $today->copy()->setTime(7, 50),
        ]);
        QueueStatusHistory::create([
            'queue_entry_id' => $q1->id,
            'changed_by' => $healthOfficer->id,
            'from_status' => 'waiting_triage',
            'to_status' => 'waiting_call',
            'reason' => 'Triase Hijau selesai',
            'created_at' => $today->copy()->setTime(8, 0),
        ]);

        QueueStatusHistory::create([
            'queue_entry_id' => $q2->id,
            'changed_by' => $healthOfficer->id,
            'from_status' => null,
            'to_status' => 'waiting_call',
            'reason' => 'Pendaftaran Darurat Merah - Respons Segera Diaktifkan',
            'created_at' => $today->copy()->setTime(8, 5),
        ]);

        QueueStatusHistory::create([
            'queue_entry_id' => $q3->id,
            'changed_by' => $registrar->id,
            'from_status' => null,
            'to_status' => 'waiting_triage',
            'reason' => 'Pendaftaran walk-in loket',
            'created_at' => $today->copy()->setTime(8, 10),
        ]);
    }
}
