<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── 1. LEAVE REQUESTS (Pengajuan Cuti) ──────────────────────────────
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('letter_date');
            $table->string('subject')->default('Izin Cuti');
            $table->string('recipient')->default('Yth. Direktur IME Medical Center di Tempat');
            $table->string('applicant_name');
            $table->string('position');
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('duration_days')->default(1);
            $table->text('reason_ic');
            $table->text('reason_ooc');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        // ─── 2. RESIGNATION REQUESTS (Pengajuan Resign Multi-Step) ───────────
        Schema::create('resignation_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('letter_date');
            $table->string('applicant_name');
            $table->string('position');
            $table->string('managerial_position')->nullable();
            $table->string('batch')->nullable();
            $table->text('reason_ic');
            $table->text('reason_ooc');
            $table->text('standard_text')->nullable();
            // Alur multi-step
            $table->enum('status', ['pending_pnd', 'approved_pnd', 'pending_ie', 'completed', 'rejected'])
                  ->default('pending_pnd');
            // PND Approval
            $table->foreignId('pnd_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('pnd_approved_at')->nullable();
            $table->text('pnd_notes')->nullable();
            // IE Denda
            $table->unsignedBigInteger('base_salary')->default(0);
            $table->decimal('fine_percentage', 5, 2)->default(30);
            $table->unsignedBigInteger('fine_amount')->default(0);
            $table->boolean('fine_paid')->default(false);
            $table->foreignId('ie_verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('ie_verified_at')->nullable();
            $table->text('ie_notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        // ─── 3. MEMBER CERTIFICATIONS (Auto-Sync Profil) ─────────────────────
        Schema::create('member_certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type');         // vehicle_land, vehicle_heli, visum_alive, visum_dead, operation_cert, medical_contract
            $table->string('division');     // ga, msl, pnd, ie
            $table->string('title');
            $table->string('certificate_number')->nullable();
            $table->foreignId('issued_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->string('file_path')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'expired', 'revoked'])->default('active');
            $table->timestamps();

            $table->index(['user_id', 'type', 'status']);
        });

        // ─── 4. STASE APPLICATIONS (Pengajuan Stase MSL & Konsulen) ──────────
        Schema::create('stase_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('konsulen_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('stase_name');
            $table->string('department')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('notes')->nullable();
            // Approval flow
            $table->enum('status', ['pending_konsulen', 'approved_konsulen', 'pending_msl', 'approved', 'rejected', 'completed'])
                  ->default('pending_konsulen');
            // Konsulen
            $table->foreignId('konsulen_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('konsulen_approved_at')->nullable();
            $table->text('konsulen_notes')->nullable();
            // MSL
            $table->foreignId('msl_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('msl_approved_at')->nullable();
            $table->text('msl_notes')->nullable();
            // Hasil
            $table->boolean('passed')->nullable();
            $table->string('grade')->nullable();
            $table->foreignId('certification_id')->nullable()->constrained('member_certifications')->nullOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        // ─── 5. OPERATION REQUESTS (Pengajuan Tindakan Operasi PND) ──────────
        Schema::create('operation_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('jenis_operasi');  // Operasi Minor, Operasi Mayor, dll.
            $table->string('patient_name');
            $table->text('diagnosis');
            $table->text('planned_procedure');
            $table->dateTime('scheduled_at')->nullable();
            $table->foreignId('dpjp_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('assistant_ids')->nullable(); // array of user IDs
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->foreignId('verified_by_pnd')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('pnd_verified_at')->nullable();
            $table->text('pnd_notes')->nullable();
            $table->foreignId('operation_record_id')->nullable()->constrained('operation_records')->nullOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        // ─── 6. PROMOTION PERIODS (Kontrol Periode Kenaikan Jabatan PND) ─────
        Schema::create('promotion_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('batch')->nullable();
            $table->string('hospital')->default('alta');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_open')->default(false);
            $table->foreignId('opened_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['hospital', 'is_open']);
        });

        // ─── 7. PROMOTION APPLICATIONS (Pengajuan Kenaikan Jabatan) ──────────
        Schema::create('promotion_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('period_id')->constrained('promotion_periods')->cascadeOnDelete();
            $table->foreignId('current_role_id')->nullable()->constrained('staff_roles')->nullOnDelete();
            $table->foreignId('target_role_id')->nullable()->constrained('staff_roles')->nullOnDelete();
            $table->integer('credit_score_at_submission')->default(0);
            $table->integer('training_days')->default(0);
            $table->float('duty_hours')->default(0);
            $table->json('requirements_checklist')->nullable(); // snapshot {key, label, met}
            $table->string('case_study_file')->nullable();
            $table->string('recommendation_letter_1')->nullable();
            $table->string('recommendation_letter_2')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by_pnd')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('pnd_reviewed_at')->nullable();
            $table->text('pnd_notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'period_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_applications');
        Schema::dropIfExists('promotion_periods');
        Schema::dropIfExists('operation_requests');
        Schema::dropIfExists('stase_applications');
        Schema::dropIfExists('member_certifications');
        Schema::dropIfExists('resignation_requests');
        Schema::dropIfExists('leave_requests');
    }
};
