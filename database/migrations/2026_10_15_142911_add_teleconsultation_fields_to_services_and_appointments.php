<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->boolean('supports_teleconsultation')->default(false)->after('is_active')->index();
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->string('consultation_type', 20)->default('in_person')->after('slot_time')->index(); // in_person, video
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex(['consultation_type']);
            $table->dropColumn('consultation_type');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex(['supports_teleconsultation']);
            $table->dropColumn('supports_teleconsultation');
        });
    }
};
