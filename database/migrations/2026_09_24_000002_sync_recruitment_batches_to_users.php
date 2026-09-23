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
        if (Schema::hasTable('users') && Schema::hasTable('recruitment_applications') && Schema::hasTable('recruitment_periods')) {
            if (Schema::hasColumn('users', 'batch') && Schema::hasColumn('recruitment_periods', 'batch_name')) {
                try {
                    DB::statement("
                        UPDATE users u
                        JOIN recruitment_applications ra ON (ra.user_id = u.id OR LOWER(TRIM(ra.cid)) = LOWER(TRIM(u.citizen_id)) OR LOWER(TRIM(ra.cid)) = LOWER(TRIM(u.staff_id)))
                        JOIN recruitment_periods rp ON rp.id = ra.period_id
                        SET u.batch = rp.batch_name
                        WHERE (u.batch IS NULL OR u.batch = '')
                          AND rp.batch_name IS NOT NULL
                          AND rp.batch_name != ''
                    ");
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Migration sync_recruitment_batches_to_users failed: ' . $e->getMessage());
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reversal needed for batch data synchronization
    }
};
