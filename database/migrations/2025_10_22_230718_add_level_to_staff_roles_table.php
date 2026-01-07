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
        if (!Schema::hasTable('staff_roles')) {
            return;
        }

        Schema::table('staff_roles', function (Blueprint $table) {
            $table->integer('level')->default(0)->after('display_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('staff_roles')) {
            return;
        }

        Schema::table('staff_roles', function (Blueprint $table) {
            $table->dropColumn('level');
        });
    }
};
