<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 20)->unique()->index();
            $table->string('name')->index();
            $table->date('date_of_birth');
            $table->enum('gender', ['L', 'P']);
            $table->string('phone', 25)->index();
            $table->text('address')->nullable();
            $table->string('bpjs_number', 30)->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
