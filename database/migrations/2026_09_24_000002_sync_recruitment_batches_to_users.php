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
                    // Safe & fast update using indexed foreign keys only (no table-locking Cartesian OR conditions)
                    DB::statement("
                        UPDATE users u
                        INNER JOIN recruitment_applications ra ON ra.user_id = u.id
                        INNER JOIN recruitment_periods rp ON rp.id = ra.period_id
                        SET u.batch = rp.batch_name
                        WHERE (u.batch IS NULL OR u.batch = '')
                          AND rp.batch_name IS NOT NULL
                          AND rp.batch_name != ''
                    ");

                    // Secondary safe update by citizen_id matching
                    if (Schema::hasColumn('recruitment_applications', 'cid') && Schema::hasColumn('users', 'citizen_id')) {
                        DB::statement("
                            UPDATE users u
                            INNER JOIN recruitment_applications ra ON ra.cid = u.citizen_id
                            INNER JOIN recruitment_periods rp ON rp.id = ra.period_id
                            SET u.batch = rp.batch_name
                            WHERE (u.batch IS NULL OR u.batch = '')
                              AND ra.cid IS NOT NULL AND ra.cid != ''
                              AND rp.batch_name IS NOT NULL AND rp.batch_name != ''
                        ");
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Migration sync_recruitment_batches_to_users skipped: ' . $e->getMessage());
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
