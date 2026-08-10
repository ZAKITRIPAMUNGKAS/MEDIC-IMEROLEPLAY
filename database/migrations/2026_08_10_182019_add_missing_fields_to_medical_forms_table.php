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
        Schema::table('medical_forms', function (Blueprint $table) {
            if (!Schema::hasColumn('medical_forms', 'hospital')) {
                $table->enum('hospital', ['alta', 'roxwood'])->default('alta')->after('form_type');
            }
            if (!Schema::hasColumn('medical_forms', 'testimoni')) {
                $table->text('testimoni')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('medical_forms', 'rating')) {
                $table->tinyInteger('rating')->nullable()->after('testimoni');
            }
            if (!Schema::hasColumn('medical_forms', 'testimoni_approved')) {
                $table->boolean('testimoni_approved')->default(false)->after('rating');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_forms', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('medical_forms', 'hospital')) {
                $columns[] = 'hospital';
            }
            if (Schema::hasColumn('medical_forms', 'testimoni')) {
                $columns[] = 'testimoni';
            }
            if (Schema::hasColumn('medical_forms', 'rating')) {
                $columns[] = 'rating';
            }
            if (Schema::hasColumn('medical_forms', 'testimoni_approved')) {
                $columns[] = 'testimoni_approved';
            }
            
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
