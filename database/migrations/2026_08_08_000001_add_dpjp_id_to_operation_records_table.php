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
            // Add dpjp_id column after created_by
            $table->unsignedBigInteger('dpjp_id')->nullable()->after('created_by');
            $table->foreign('dpjp_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operation_records', function (Blueprint $table) {
            $table->dropForeign(['dpjp_id']);
            $table->dropColumn('dpjp_id');
        });
    }
};
