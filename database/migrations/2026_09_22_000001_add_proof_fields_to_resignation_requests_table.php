<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Tambah kolom bukti upload resign dan pencatatan verifikasi penonaktifan akhir.
     */
    public function up(): void
    {
        Schema::table('resignation_requests', function (Blueprint $table) {
            // Bukti-bukti yang wajib diunggah anggota
            $table->string('pocket_proof')->nullable()->after('ie_notes')->comment('Foto Kantong - screenshot full layar');
            $table->string('key_proof')->nullable()->after('pocket_proof')->comment('Foto Kunci - memastikan kunci sudah tercabut');
            $table->string('letter_proof')->nullable()->after('key_proof')->comment('Foto Surat Resign');
            $table->string('fine_proof')->nullable()->after('letter_proof')->comment('Foto Billing Denda Resign');
            
            // Catatan & timestamp pengunggahan bukti
            $table->timestamp('proof_submitted_at')->nullable()->after('fine_proof');
            $table->text('proof_revision_notes')->nullable()->after('proof_submitted_at')->comment('Catatan jika IE meminta pengisian ulang bukti');

            // IE yang melakukan konfirmasi penonaktifan akhir
            $table->foreignId('final_deactivated_by')->nullable()->after('proof_revision_notes')->constrained('users')->nullOnDelete();
            $table->timestamp('final_deactivated_at')->nullable()->after('final_deactivated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resignation_requests', function (Blueprint $table) {
            $table->dropForeign(['final_deactivated_by']);
            $table->dropColumn([
                'pocket_proof',
                'key_proof',
                'letter_proof',
                'fine_proof',
                'proof_submitted_at',
                'proof_revision_notes',
                'final_deactivated_by',
                'final_deactivated_at',
            ]);
        });
    }
};
