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
            // Add missing columns for session management
            if (!Schema::hasColumn('attendances', 'session_number')) {
                $table->integer('session_number')->default(1)->after('work_date');
            }
            if (!Schema::hasColumn('attendances', 'session_type')) {
                $table->string('session_type')->default('work')->after('session_number');
            }
            if (!Schema::hasColumn('attendances', 'is_active')) {
                $table->boolean('is_active')->default(false)->after('session_type');
            }
            if (!Schema::hasColumn('attendances', 'session_duration')) {
                $table->integer('session_duration')->nullable()->after('is_active');
            }
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
            // Remove added columns
            $table->dropColumn(['session_number', 'session_type', 'is_active', 'session_duration']);
        });
    }
};
