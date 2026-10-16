<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('triage_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('queue_entry_id')->constrained('queue_entries')->onDelete('cascade');
            $table->foreignId('assessed_by')->constrained('users')->onDelete('cascade'); // Health officer
            $table->string('category', 20)->index(); // 'merah' (emergency), 'kuning' (urgent), 'hijau' (non-urgent), 'hitam' (meninggal)
            $table->integer('systolic_bp')->nullable();
            $table->integer('diastolic_bp')->nullable();
            $table->integer('heart_rate')->nullable();
            $table->integer('respiratory_rate')->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->integer('oxygen_saturation')->nullable();
            $table->string('consciousness_level', 30)->default('compos_mentis'); // compos_mentis, somnolen, sopor, coma
            $table->text('chief_complaint');
            $table->text('observations')->nullable();
            $table->boolean('is_immediate_response_triggered')->default(false)->index();
            $table->timestamp('assessed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('triage_assessments');
    }
};
