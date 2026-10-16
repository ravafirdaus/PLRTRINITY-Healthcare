<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropUnique('patient_service_date_unique');
        });

        $driver = DB::getDriverName();
        if (in_array($driver, ['pgsql', 'sqlite'])) {
            DB::statement("CREATE UNIQUE INDEX patient_service_date_unique ON appointments (patient_id, service_id, appointment_date) WHERE status IN ('booked', 'checked_in')");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        if (in_array($driver, ['pgsql', 'sqlite'])) {
            DB::statement('DROP INDEX IF EXISTS patient_service_date_unique');
        }

        Schema::table('appointments', function (Blueprint $table) {
            $table->unique(['patient_id', 'service_id', 'appointment_date'], 'patient_service_date_unique');
        });
    }
};
