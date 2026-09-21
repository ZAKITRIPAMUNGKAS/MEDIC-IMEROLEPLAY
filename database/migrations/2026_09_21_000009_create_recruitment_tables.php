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
        // 1. recruitment_periods
        if (!Schema::hasTable('recruitment_periods')) {
            Schema::create('recruitment_periods', function (Blueprint $table) {
                $table->id();
                $table->string('hospital', 50)->default('alta');
                $table->string('batch_name');
                $table->boolean('is_open')->default(false);
                $table->foreignId('opened_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('opened_at')->nullable();
                $table->timestamp('closed_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['hospital', 'is_open']);
            });
        }

        // 2. recruitment_applications
        if (!Schema::hasTable('recruitment_applications')) {
            Schema::create('recruitment_applications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('period_id')->nullable()->constrained('recruitment_periods')->nullOnDelete();
                $table->string('hospital', 50)->default('alta');

                // Persyaratan IC
                $table->boolean('agree_general_req')->default(false);
                $table->boolean('agree_special_req')->default(false);

                // Identitas IC (Curriculum Vitae IC)
                $table->string('ic_name');
                $table->string('cid');
                $table->enum('gender', ['Laki-laki', 'Perempuan'])->default('Laki-laki');
                $table->date('birth_date');
                $table->string('has_medical_exp', 20)->default('Tidak');
                $table->text('medical_exp_desc')->nullable();
                $table->text('reason_joining'); // Minimal 50 kata
                $table->text('rp_experience');

                // Lampiran Berkas
                $table->string('ktp_file');
                $table->string('skb_file');
                $table->string('health_cert_file');
                $table->string('psychology_cert_file')->nullable();

                // Informasi OOC
                $table->text('other_city_responsibility')->nullable();
                $table->json('online_hours')->nullable(); // e.g. ["00:00 - 06:00", "19:00 - 24:00"]
                $table->json('online_days')->nullable();  // e.g. ["Every Day", "Weekend"]
                $table->string('discord_username')->nullable();

                // Status & Review PND/IE
                $table->enum('status', ['pending', 'reviewed', 'interview', 'accepted', 'rejected'])->default('pending');
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable();
                $table->text('reviewer_notes')->nullable();

                // Referensi Akun jika lolos / dikonversi
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

                $table->timestamps();

                $table->index(['hospital', 'status', 'period_id']);
                $table->index('cid');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recruitment_applications');
        Schema::dropIfExists('recruitment_periods');
    }
};
