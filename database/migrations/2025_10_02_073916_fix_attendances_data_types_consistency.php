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
        // Fix data type consistency in attendances table
        Schema::table('attendances', function (Blueprint $table) {
            // Rename total_hours to total_seconds for clarity
            $table->renameColumn('total_hours', 'total_seconds');
        });
        
        // Update existing data to convert minutes to seconds
        DB::statement('UPDATE attendances SET total_seconds = total_seconds * 60 WHERE total_seconds IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the changes
        Schema::table('attendances', function (Blueprint $table) {
            $table->renameColumn('total_seconds', 'total_hours');
        });
        
        // Convert back to minutes
        DB::statement('UPDATE attendances SET total_hours = total_hours / 60 WHERE total_hours IS NOT NULL');
    }
};
