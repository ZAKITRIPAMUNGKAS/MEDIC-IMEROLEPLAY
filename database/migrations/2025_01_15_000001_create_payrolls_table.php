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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('period_start'); // Tanggal mulai periode gaji
            $table->date('period_end'); // Tanggal akhir periode gaji
            $table->integer('total_hours'); // Total jam kerja dalam detik
            $table->integer('base_salary'); // Gaji pokok berdasarkan role
            $table->integer('calculated_salary'); // Gaji yang dihitung
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->timestamp('paid_at')->nullable(); // Tanggal dibayar
            $table->foreignId('paid_by')->nullable()->constrained('users')->onDelete('set null'); // Admin yang membayar
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->timestamps();
            
            // Index untuk performa query
            $table->index(['user_id', 'period_start', 'period_end']);
            $table->index('status');
            $table->index('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
