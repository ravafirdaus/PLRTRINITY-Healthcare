<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teleconsultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->unique()->constrained('appointments')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('session_id', 64)->unique();
            $table->string('room_name', 100)->unique();
            $table->string('status', 30)->default('scheduled')->index(); // scheduled, waiting_doctor, doctor_ready, in_progress, completed, cancelled
            $table->timestamp('scheduled_at')->index();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->timestamp('doctor_joined_at')->nullable();
            $table->timestamp('patient_joined_at')->nullable();
            $table->boolean('patient_consent_given')->default(false);
            $table->timestamp('patient_consent_at')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('clinical_notes')->nullable();
            $table->string('disposition', 50)->nullable();
            $table->timestamps();

            $table->index(['service_id', 'status', 'scheduled_at']);
            $table->index(['doctor_id', 'status', 'scheduled_at']);
            $table->index(['patient_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teleconsultations');
    }
};
