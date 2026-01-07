<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if table exists before modifying
        if (!Schema::hasTable('payrolls')) {
            return;
        }

        // First, convert existing data from seconds to hours
        DB::statement('UPDATE payrolls SET total_hours = total_hours / 3600');
        
        // Then change the column type
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('total_hours', 8, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('payrolls')) {
            return;
        }

        // Convert hours back to seconds
        DB::statement('UPDATE payrolls SET total_hours = total_hours * 3600');
        
        // Change back to integer
        Schema::table('payrolls', function (Blueprint $table) {
            $table->integer('total_hours')->change();
        });
    }
};