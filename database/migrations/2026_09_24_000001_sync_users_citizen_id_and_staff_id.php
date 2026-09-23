<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            // 1. Sync staff_id from citizen_id if staff_id is empty/null
            if (Schema::hasColumn('users', 'staff_id') && Schema::hasColumn('users', 'citizen_id')) {
                DB::statement("UPDATE users SET staff_id = citizen_id WHERE (staff_id IS NULL OR staff_id = '') AND citizen_id IS NOT NULL AND citizen_id != ''");
                
                // 2. Sync citizen_id from staff_id if citizen_id is empty/null
                DB::statement("UPDATE users SET citizen_id = staff_id WHERE (citizen_id IS NULL OR citizen_id = '') AND staff_id IS NOT NULL AND staff_id != ''");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reversal needed for data synchronization
    }
};
