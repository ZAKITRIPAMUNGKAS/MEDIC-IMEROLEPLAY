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
        if (Schema::hasTable('attendance') && !Schema::hasTable('attendances')) {
            Schema::rename('attendance', 'attendances');
            return;
        }

        if (!Schema::hasTable('attendances')) {
            Schema::create('attendances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users');
                $table->timestamp('clock_in');
                $table->timestamp('clock_out')->nullable();
                $table->integer('total_hours')->nullable();
                $table->date('work_date');
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'work_date']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('attendances') && !Schema::hasTable('attendance')) {
            Schema::rename('attendances', 'attendance');
            return;
        }

        if (Schema::hasTable('attendances')) {
            Schema::drop('attendances');
        }
    }
};


