<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * Tambah kolom bukti upload resign, perluas enum status menjadi varchar, dan pencatatan verifikasi penonaktifan akhir.
     */
    public function up(): void
    {
        // 1. Pastikan kolom status diubah menjadi VARCHAR(50) agar tidak dibatasi ENUM lama
        try {
            DB::statement("ALTER TABLE resignation_requests MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'pending_pnd'");
        } catch (\Throwable $e) {}

        // 2. Tambah kolom bukti dan penonaktifan jika belum ada
        Schema::table('resignation_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('resignation_requests', 'pocket_proof')) {
                $table->string('pocket_proof')->nullable()->comment('Foto Kantong - screenshot full layar');
            }
            if (!Schema::hasColumn('resignation_requests', 'key_proof')) {
                $table->string('key_proof')->nullable()->comment('Foto Kunci - memastikan kunci sudah tercabut');
            }
            if (!Schema::hasColumn('resignation_requests', 'letter_proof')) {
                $table->string('letter_proof')->nullable()->comment('Foto Surat Resign');
            }
            if (!Schema::hasColumn('resignation_requests', 'fine_proof')) {
                $table->string('fine_proof')->nullable()->comment('Foto Billing Denda Resign');
            }
            if (!Schema::hasColumn('resignation_requests', 'proof_submitted_at')) {
                $table->timestamp('proof_submitted_at')->nullable();
            }
            if (!Schema::hasColumn('resignation_requests', 'proof_revision_notes')) {
                $table->text('proof_revision_notes')->nullable()->comment('Catatan jika IE meminta pengisian ulang bukti');
            }
            if (!Schema::hasColumn('resignation_requests', 'final_deactivated_by')) {
                $table->foreignId('final_deactivated_by')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('resignation_requests', 'final_deactivated_at')) {
                $table->timestamp('final_deactivated_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resignation_requests', function (Blueprint $table) {
            if (Schema::hasColumn('resignation_requests', 'final_deactivated_by')) {
                $table->dropForeign(['final_deactivated_by']);
                $table->dropColumn('final_deactivated_by');
            }
            $cols = ['pocket_proof', 'key_proof', 'letter_proof', 'fine_proof', 'proof_submitted_at', 'proof_revision_notes', 'final_deactivated_at'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('resignation_requests', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
