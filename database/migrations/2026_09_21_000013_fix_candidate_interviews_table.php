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
        if (Schema::hasTable('candidate_interviews')) {
            Schema::table('candidate_interviews', function (Blueprint $table) {
                if (!Schema::hasColumn('candidate_interviews', 'recruitment_application_id')) {
                    $table->unsignedBigInteger('recruitment_application_id')->nullable()->after('user_id');
                }
            });

            // Make user_id nullable so non-registered candidates can be evaluated
            try {
                DB::statement('ALTER TABLE candidate_interviews MODIFY user_id BIGINT UNSIGNED NULL');
            } catch (\Throwable $e) {
                // Ignore if already nullable or unsupported
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('candidate_interviews') && Schema::hasColumn('candidate_interviews', 'recruitment_application_id')) {
            Schema::table('candidate_interviews', function (Blueprint $table) {
                $table->dropColumn('recruitment_application_id');
            });
        }
    }
};
