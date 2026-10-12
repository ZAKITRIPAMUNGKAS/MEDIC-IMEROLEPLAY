<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel saldo credit score per anggota
        if (!Schema::hasTable('credit_scores')) {
            Schema::create('credit_scores', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->integer('balance')->default(100); // saldo awal 100
                $table->timestamps();

                $table->unique('user_id'); // satu baris per user
            });
        }

        // Tabel log transaksi credit score (setiap penambahan/pengurangan)
        if (!Schema::hasTable('credit_score_logs')) {
            Schema::create('credit_score_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete(); // Comdis yang input
                $table->integer('amount');          // positif = tambah, negatif = kurang
                $table->integer('balance_after');   // saldo setelah transaksi
                $table->text('reason')->nullable(); // alasan penambahan/pengurangan
                $table->string('type', 20)->default('add');
                $table->timestamps();

                $table->index(['user_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_score_logs');
        Schema::dropIfExists('credit_scores');
    }
};
