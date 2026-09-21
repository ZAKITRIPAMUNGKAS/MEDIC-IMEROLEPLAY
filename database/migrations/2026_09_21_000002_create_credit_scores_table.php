<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel saldo credit score per anggota
        Schema::create('credit_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('balance')->default(100); // saldo awal 100
            $table->timestamps();

            $table->unique('user_id'); // satu baris per user
        });

        // Tabel log transaksi credit score (setiap penambahan/pengurangan)
        Schema::create('credit_score_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('issued_by')->constrained('users')->cascadeOnDelete(); // Comdis yang input
            $table->integer('amount');          // positif = tambah, negatif = kurang
            $table->integer('balance_after');   // saldo setelah transaksi
            $table->string('reason');           // alasan penambahan/pengurangan
            $table->enum('type', ['add', 'deduct'])->default('add');
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_score_logs');
        Schema::dropIfExists('credit_scores');
    }
};
