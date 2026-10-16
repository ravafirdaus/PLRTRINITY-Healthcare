<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultation_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('queue_entry_id')->constrained('queue_entries')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->text('clinical_notes')->nullable();
            $table->string('disposition', 30)->default('pulang'); // pulang, rujuk_lanjut, rawat_inap, kontrol_ulang
            $table->timestamps();

            $table->index(['doctor_id', 'service_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultation_sessions');
    }
};
