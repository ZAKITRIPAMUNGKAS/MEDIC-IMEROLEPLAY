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
        Schema::create('payroll_exports', function (Blueprint $table) {
            $table->id();
            $table->integer('export_year');           // Tahun export
            $table->tinyInteger('export_month');   // Bulan export (1-12)
            $table->unsignedBigInteger('exported_by'); // User yang export
            $table->timestamp('exported_at');      // Waktu export
            $table->json('filters')->nullable();   // Filter yang digunakan saat export
            $table->integer('records_count')->default(0); // Jumlah records yang di-export
            $table->timestamps();

            // Removed foreign key to avoid migration issues
            // $table->foreign('exported_by')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['export_year', 'export_month']); // Hanya 1 export per bulan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_exports');
    }
};
