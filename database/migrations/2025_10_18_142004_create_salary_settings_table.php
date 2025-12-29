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
        Schema::create('salary_settings', function (Blueprint $table) {
            $table->id();
            $table->string('role_name')->unique(); // Nama role (Admin, Staff, Manager, dll)
            $table->decimal('hourly_rate', 10, 2); // Gaji per jam
            $table->decimal('overtime_rate', 10, 2)->nullable(); // Gaji lembur per jam (optional)
            $table->integer('overtime_threshold')->default(8); // Threshold jam lembur (default 8 jam)
            $table->text('description')->nullable(); // Deskripsi role
            $table->boolean('is_active')->default(true); // Status aktif/tidak
            $table->timestamps();
            
            $table->index(['role_name', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_settings');
    }
};