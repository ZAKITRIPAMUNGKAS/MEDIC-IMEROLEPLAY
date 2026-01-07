<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if table exists before modifying
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('hospital', 20)
                ->default('alta')
                ->after('staff_id')
                ->comment('Hospital assignment: alta or roxwood');
        });

        // Migrate existing Roxwood users (with RH prefix)
        DB::statement("
            UPDATE users 
            SET hospital = 'roxwood' 
            WHERE LOWER(name) LIKE '%rh%' 
               OR LOWER(name) LIKE '%roxwood%'
               OR LOWER(staff_id) LIKE '%rh%'
               OR LOWER(staff_id) LIKE '%rh -%'
               OR LOWER(staff_id) LIKE '%rh-%'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('hospital');
        });
    }
};
