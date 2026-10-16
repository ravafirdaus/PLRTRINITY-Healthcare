<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('queue_entry_id')->constrained('queue_entries')->onDelete('cascade');
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('from_status', 25)->nullable();
            $table->string('to_status', 25)->index();
            $table->text('reason')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();

            $table->index(['queue_entry_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_status_histories');
    }
};
