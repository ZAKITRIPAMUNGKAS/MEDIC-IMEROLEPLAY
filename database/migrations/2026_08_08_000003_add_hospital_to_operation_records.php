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
        Schema::table('operation_records', function (Blueprint $table) {
            $table->string('hospital')->nullable()->default('roxwood')->after('jenis_operasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operation_records', function (Blueprint $table) {
            $table->dropColumn('hospital');
        });
    }
};
