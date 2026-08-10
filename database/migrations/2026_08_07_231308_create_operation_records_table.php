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
        Schema::create('operation_records', function (Blueprint $table) {
            $table->id();
            $table->dateTime('tanggal_waktu');
            $table->string('lokasi');
            $table->enum('jenis_operasi', ['Operasi Minor', 'Operasi Mayor', 'Emergency', 'Bedah Umum', 'Orthopedi', 'Lainnya']);
            $table->string('nama_pasien');
            $table->text('diagnosa');
            $table->text('tindakan_operasi');
            $table->text('hasil_operasi');
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_records');
    }
};
