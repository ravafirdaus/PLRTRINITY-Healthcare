<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_number', 30)->unique();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->date('appointment_date')->index();
            $table->time('slot_time')->index();
            $table->string('status', 20)->default('booked')->index(); // booked, checked_in, cancelled, completed, no_show
            $table->text('notes')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamps();

            // Unique constraint to prevent duplicate booking for same patient on same service on same date
            $table->unique(['patient_id', 'service_id', 'appointment_date'], 'patient_service_date_unique');
            $table->index(['service_id', 'appointment_date', 'slot_time', 'status'], 'svc_date_slot_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
