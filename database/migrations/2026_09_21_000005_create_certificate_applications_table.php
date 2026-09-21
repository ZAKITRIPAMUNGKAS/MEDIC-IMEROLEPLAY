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
        if (!Schema::hasTable('certificate_applications')) {
            Schema::create('certificate_applications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('type'); // 'vehicle_land', 'vehicle_heli', 'operation_cert'
                $table->string('division'); // 'ga', 'pnd'
                $table->string('title');
                $table->text('reason')->nullable();
                $table->text('notes')->nullable();
                $table->string('status', 30)->default('pending'); // 'pending', 'approved', 'rejected'
                $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('verified_at')->nullable();
                $table->text('admin_notes')->nullable();
                $table->foreignId('certification_id')->nullable()->constrained('member_certifications')->nullOnDelete();
                $table->timestamps();

                $table->index(['user_id', 'division', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_applications');
    }
};
