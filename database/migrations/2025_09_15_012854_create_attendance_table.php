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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamp('clock_in');
            $table->timestamp('clock_out')->nullable();
            $table->integer('total_hours')->nullable(); // Total jam kerja dalam menit
            $table->date('work_date'); // Tanggal kerja
            $table->text('notes')->nullable(); // Catatan shift
            $table->timestamps();
            
            $table->index(['user_id', 'work_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
