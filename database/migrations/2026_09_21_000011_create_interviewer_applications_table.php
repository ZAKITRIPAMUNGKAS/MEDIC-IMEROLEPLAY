<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('interviewer_applications')) {
            Schema::create('interviewer_applications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('hospital', 50)->default('alta');
                $table->text('reason')->nullable();
                $table->string('status', 30)->default('pending'); // pending, approved, rejected, revoked
                $table->foreignId('action_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('action_at')->nullable();
                $table->text('action_notes')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('interviewer_applications');
    }
};
