<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only add columns if table exists
        if (!Schema::hasTable('medical_forms')) {
            return;
        }

        Schema::table('medical_forms', function (Blueprint $table) {
            $table->text('testimoni')->nullable()->after('notes');
            $table->tinyInteger('rating')->nullable()->after('testimoni');
            $table->boolean('testimoni_approved')->default(false)->after('rating');
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
            $table->dropColumn(['testimoni', 'rating', 'testimoni_approved']);
        });
    }
};

