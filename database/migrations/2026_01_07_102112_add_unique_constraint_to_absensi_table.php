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
            DELETE t1 FROM absensi t1
            INNER JOIN absensi t2 
            WHERE t1.id > t2.id 
            AND t1.player_id = t2.player_id 
            AND t1.clock_in = t2.clock_in
        ");

        Schema::table('absensi', function (Blueprint $table) {
            // Tambahkan unique constraint untuk mencegah duplikasi
            // Kombinasi player_id + clock_in harus unik
            $table->unique(['player_id', 'clock_in'], 'unique_absensi_entry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            // Hapus unique constraint saat rollback
            $table->dropUnique('unique_absensi_entry');
        });
    }
};
