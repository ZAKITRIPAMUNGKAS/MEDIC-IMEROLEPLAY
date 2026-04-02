<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * Add 'offline' to the status enum to properly track when user is NOT on duty.
     * Previously, users were set to 'working' on clock-out, causing ghost on-duty counts.
     */
    public function up(): void
    {
        // MySQL ENUM modification requires raw SQL
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('working', 'meeting', 'offline') NOT NULL DEFAULT 'offline' COMMENT 'Current staff status: working on duty, in meeting, or offline'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First reset any 'offline' users back to 'working' before reverting enum
        DB::table('users')->where('status', 'offline')->update(['status' => 'working']);
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('working', 'meeting') NOT NULL DEFAULT 'working' COMMENT 'Current staff status: working on duty or in meeting'");
    }
};
