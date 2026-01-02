<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add new values to enum temporarily
        DB::statement("ALTER TABLE `feedback` MODIFY COLUMN `type` ENUM('kritik', 'saran', 'laporan', 'masukan') NOT NULL");

        // Step 2: Update existing data
        DB::table('feedback')->where('type', 'kritik')->update(['type' => 'laporan']);
        DB::table('feedback')->where('type', 'saran')->update(['type' => 'masukan']);

        // Step  3: Remove old values from enum
        DB::statement("ALTER TABLE `feedback` MODIFY COLUMN `type` ENUM('laporan', 'masukan') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add old values back
        DB::statement("ALTER TABLE `feedback` MODIFY COLUMN `type` ENUM('kritik', 'saran', 'laporan', 'masukan') NOT NULL");

        // Revert data
        DB::table('feedback')->where('type', 'laporan')->update(['type' => 'kritik']);
        DB::table('feedback')->where('type', 'masukan')->update(['type' => 'saran']);

        // Remove new values
        DB::statement("ALTER TABLE `feedback` MODIFY COLUMN `type` ENUM('kritik', 'saran') NOT NULL");
    }
};
