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
        Schema::create('payroll_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained('payrolls')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('notification_type', ['salary_paid', 'salary_pending', 'salary_reminder']);
            $table->timestamp('sent_at')->nullable(); // Tanggal notifikasi dikirim
            $table->enum('status', ['sent', 'failed', 'pending'])->default('pending');
            $table->text('message')->nullable(); // Pesan notifikasi
            $table->json('metadata')->nullable(); // Data tambahan untuk notifikasi
            $table->timestamps();
            
            // Index untuk performa query
            $table->index(['user_id', 'notification_type']);
            $table->index('status');
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_notifications');
    }
};
