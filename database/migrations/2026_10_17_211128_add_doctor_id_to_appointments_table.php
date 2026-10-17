<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('doctor_id')->nullable()->after('patient_id')->constrained('users')->nullOnDelete();
            $table->index(['doctor_id', 'appointment_date']);
        });

        $driver = DB::getDriverName();
        if (in_array($driver, ['pgsql', 'sqlite'])) {
            DB::statement('DROP INDEX IF EXISTS patient_service_date_unique');
            DB::statement("CREATE UNIQUE INDEX patient_service_date_unique ON appointments (patient_id, service_id, appointment_date) WHERE status IN ('booked', 'checked_in')");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['doctor_id']);
            $table->dropIndex(['doctor_id', 'appointment_date']);
            $table->dropColumn('doctor_id');
        });
    }
};
