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
        Schema::create('user_rename_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_name')->nullable(); // Nama batch (opsional)
            $table->text('description')->nullable(); // Deskripsi batch
            $table->integer('total_users')->default(0); // Total user yang akan direname
            $table->integer('successful_renames')->default(0); // Jumlah rename berhasil
            $table->integer('failed_renames')->default(0); // Jumlah rename gagal
            $table->json('mapping_data')->nullable(); // Data mapping (old_name => new_name)
            $table->json('rename_log')->nullable(); // Log detail setiap rename
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->timestamp('processed_at')->nullable(); // Waktu batch diproses
            $table->timestamps();
        });

        // Tabel untuk detail setiap rename dalam batch
        Schema::create('user_rename_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('user_rename_batches')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('old_name'); // Nama lama
            $table->string('new_name'); // Nama baru
            $table->float('similarity_score', 5, 2)->nullable(); // Skor similarity (0-100)
            $table->string('match_type')->nullable(); // exact, similar, manual
            $table->string('status')->default('pending'); // pending, success, failed
            $table->text('error_message')->nullable(); // Pesan error jika gagal
            $table->timestamp('renamed_at')->nullable(); // Waktu rename
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_rename_logs');
        Schema::dropIfExists('user_rename_batches');
    }
};
