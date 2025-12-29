<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->string('player_id');
            $table->string('player_name');
            $table->datetime('clock_in');
            $table->datetime('clock_out')->nullable();
            $table->time('time_on_duty')->nullable();
            $table->string('source')->default('automatic'); // automatic, manual, fivem
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->timestamps();
            
            // Index untuk performa query
            $table->index('player_id');
            $table->index(['player_id', 'clock_in']);
            $table->index('source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
