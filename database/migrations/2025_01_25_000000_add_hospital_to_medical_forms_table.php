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
        if (!Schema::hasTable('medical_forms')) {
            return;
        }

        Schema::table('medical_forms', function (Blueprint $table) {
            $table->enum('hospital', ['alta', 'roxwood'])->default('alta')->after('form_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('medical_forms')) {
            return;
        }

        Schema::table('medical_forms', function (Blueprint $table) {
            $table->dropColumn('hospital');
        });
    }
};

