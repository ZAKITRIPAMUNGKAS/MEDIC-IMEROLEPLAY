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
        // Check if table exists before modifying
        if (!Schema::hasTable('attendances')) {
            return;
        }

        Schema::table('attendances', function (Blueprint $table) {
            $table->integer('scheduled_duty_minutes')->nullable()->after('clock_in');
            // Durasi duty yang dijadwalkan dalam menit (null = normal mode tanpa timer)
            
            $table->timestamp('scheduled_end_time')->nullable()->after('scheduled_duty_minutes');
            // Waktu akhir yang dijadwalkan (clock_in + scheduled_duty_minutes)
            
            $table->boolean('auto_checked_out')->default(false)->after('is_active');
            // Flag untuk menandai apakah check out dilakukan otomatis oleh sistem
            
            $table->index('scheduled_end_time'); // Index untuk query auto checkout
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('attendances')) {
            return;
        }

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['scheduled_end_time']);
            $table->dropColumn(['scheduled_duty_minutes', 'scheduled_end_time', 'auto_checked_out']);
        });
    }
};
