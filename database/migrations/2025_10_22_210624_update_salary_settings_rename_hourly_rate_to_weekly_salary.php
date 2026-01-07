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
        if (!Schema::hasTable('salary_settings')) {
            return;
        }

        Schema::table('salary_settings', function (Blueprint $table) {
            $table->renameColumn('hourly_rate', 'weekly_salary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('salary_settings')) {
            return;
        }

        Schema::table('salary_settings', function (Blueprint $table) {
            $table->renameColumn('weekly_salary', 'hourly_rate');
        });
    }
};