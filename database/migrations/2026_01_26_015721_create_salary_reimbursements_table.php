<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('salary_reimbursements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manager_id')->constrained('users')->onDelete('cascade'); // Admin/manager yang membayar gaji
            $table->date('period_start'); // Tanggal mulai periode
            $table->date('period_end'); // Tanggal akhir periode
            $table->integer('total_amount')->default(0); // Total gaji yang dibayarkan manager ini dalam periode ini
            $table->integer('payroll_count')->default(0); // Jumlah gaji yang dibayarkan
            $table->boolean('is_reimbursed')->default(false); // Status sudah direimburse atau belum
            $table->foreignId('reimbursed_by')->nullable()->constrained('users')->onDelete('set null'); // Admin yang melakukan reimburse
            $table->timestamp('reimbursed_at')->nullable(); // Tanggal direimburse
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->timestamps();

            // Index untuk performa query
            $table->index(['manager_id', 'period_start', 'period_end']);
            $table->index('is_reimbursed');
            $table->index('reimbursed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_reimbursements');
    }
};
