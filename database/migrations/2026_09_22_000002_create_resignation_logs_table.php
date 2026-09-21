<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Membuat tabel riwayat administrasi Log Resign permanen.
     */
    public function up(): void
    {
        Schema::create('resignation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resignation_request_id')->nullable()->constrained('resignation_requests')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Snapshot identitas anggota (tetap utuh meskipun akun dinonaktifkan/dihapus)
            $table->string('member_name');
            $table->string('citizen_id')->nullable();
            $table->string('last_position');
            $table->string('managerial_position')->nullable();
            $table->string('batch')->nullable();
            $table->string('hospital')->default('alta');
            
            // Tanggal resign & tanggal penonaktifan
            $table->date('resignation_date');
            $table->dateTime('deactivated_at');
            
            // Alasan resign
            $table->text('reason')->nullable();
            $table->text('reason_ic')->nullable();
            $table->text('reason_ooc')->nullable();
            
            // Rincian denda
            $table->unsignedBigInteger('total_fine')->default(0);
            $table->decimal('fine_percentage', 5, 2)->default(0);
            $table->string('fine_status')->default('Lunas');
            
            // 4 Berkas Bukti yang telah diunggah
            $table->string('pocket_proof')->nullable();
            $table->string('key_proof')->nullable();
            $table->string('letter_proof')->nullable();
            $table->string('fine_proof')->nullable();
            
            // Pihak IE terkait
            $table->string('ie_verifier_name')->nullable();
            $table->string('ie_deactivator_name');
            
            // Catatan tambahan
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['hospital', 'deactivated_at']);
            $table->index('citizen_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resignation_logs');
    }
};
