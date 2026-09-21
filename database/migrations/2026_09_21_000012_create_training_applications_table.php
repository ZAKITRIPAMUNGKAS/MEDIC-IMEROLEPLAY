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
        Schema::create('training_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('training_type'); // 'operasi', 'surat_menyurat', 'visum_hidup'
            $table->string('nama_ic');
            $table->string('gender'); // 'Laki-laki', 'Perempuan'
            $table->string('phone_ic')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('batch');
            $table->json('additional_data')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index(['training_type', 'status']);
            $table->index(['training_type', 'batch']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_applications');
    }
};
