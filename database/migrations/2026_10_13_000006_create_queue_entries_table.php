<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_entries', function (Blueprint $table) {
            $table->id();
            $table->string('queue_code', 20)->index();
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('set null');
            $table->date('queue_date')->index();
            $table->string('registration_type', 20)->default('walk_in')->index(); // reservation, walk_in, emergency
            $table->tinyInteger('priority_level')->default(3)->index(); // 1=Emergency, 2=Urgent, 3=Normal
            $table->string('status', 25)->default('waiting_triage')->index(); // waiting_triage, waiting_call, called, in_consultation, completed, skipped, cancelled
            $table->timestamp('estimated_call_time')->nullable();
            $table->timestamp('called_at')->nullable();
            $table->timestamp('consultation_started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('current_counter', 50)->nullable();
            $table->timestamps();

            $table->unique(['service_id', 'queue_date', 'queue_code'], 'svc_date_code_unique');
            $table->index(['service_id', 'queue_date', 'status', 'priority_level'], 'queue_operational_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_entries');
    }
};
