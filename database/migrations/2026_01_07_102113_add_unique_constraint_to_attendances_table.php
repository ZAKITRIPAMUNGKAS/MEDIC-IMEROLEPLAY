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
        // Hapus duplikat dulu sebelum menambahkan unique constraint
        DB::statement("
            DELETE t1 FROM attendances t1
            INNER JOIN attendances t2 
            WHERE t1.id > t2.id 
            AND t1.user_id = t2.user_id 
            AND t1.work_date = t2.work_date 
            AND t1.session_number = t2.session_number
        ");

        Schema::table('attendances', function (Blueprint $table) {
            // Tambahkan unique constraint untuk mencegah duplikasi
            // Kombinasi user_id + work_date + session_number harus unik
            $table->unique(['user_id', 'work_date', 'session_number'], 'unique_attendance_session');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Hapus unique constraint saat rollback
            $table->dropUnique('unique_attendance_session');
        });
    }
};
