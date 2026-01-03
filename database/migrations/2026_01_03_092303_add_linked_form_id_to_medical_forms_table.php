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
        Schema::table('medical_forms', function (Blueprint $table) {
            // Nullable foreign key to link psychology test with psychology letter
            $table->foreignId('linked_form_id')->nullable()->after('form_type')->constrained('medical_forms')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_forms', function (Blueprint $table) {
            $table->dropForeign(['linked_form_id']);
            $table->dropColumn('linked_form_id');
        });
    }
};
