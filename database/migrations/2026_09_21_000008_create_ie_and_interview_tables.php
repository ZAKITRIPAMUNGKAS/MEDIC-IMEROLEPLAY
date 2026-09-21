<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Tabel Interview Calon Medis
        if (!Schema::hasTable('candidate_interviews')) {
            Schema::create('candidate_interviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('interviewer_id')->constrained('users')->cascadeOnDelete();
                $table->string('result'); // recommended | not_recommended
                $table->string('recommended_role')->nullable(); // trainee | perawat | co_ass | dokter_umum
                $table->text('notes')->nullable();
                $table->timestamp('interviewed_at')->useCurrent();
                $table->timestamps();

                $table->index(['user_id', 'result']);
            });
        }

        // 2. Tabel Pengecualian Pemutihan Duty (Duty Exemption)
        if (!Schema::hasTable('duty_exemptions')) {
            Schema::create('duty_exemptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('month_period'); // Format YYYY-MM e.g. 2026-09
                $table->foreignId('leave_request_id')->nullable()->constrained('leave_requests')->nullOnDelete();
                $table->foreignId('exempted_by')->constrained('users')->cascadeOnDelete();
                $table->text('reason')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'month_period']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('duty_exemptions');
        Schema::dropIfExists('candidate_interviews');
    }
};
