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
        Schema::create('medical_forms', function (Blueprint $table) {
            $table->id();
            $table->string('character_name'); // Nama karakter pemain
            $table->string('citizen_id')->nullable(); // ID warga (opsional)
            $table->string('form_type'); // Jenis form (surat_kesehatan, dll)
            $table->text('description'); // Deskripsi keluhan/permintaan
            $table->json('form_data'); // Data form dalam format JSON
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('processed_by')->nullable()->constrained('users');
            $table->timestamp('processed_at')->nullable();
            $table->text('notes')->nullable(); // Catatan dari staf
            $table->string('ip_address')->nullable(); // Untuk tracking
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_forms');
    }
};
