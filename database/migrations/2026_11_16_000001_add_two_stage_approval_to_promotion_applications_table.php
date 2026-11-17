<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('promotion_applications')) {
            // Update status column to VARCHAR if on MySQL so it can accept 'accepted'
            if (DB::getDriverName() === 'mysql') {
                try {
                    DB::statement("ALTER TABLE `promotion_applications` MODIFY COLUMN `status` VARCHAR(30) NOT NULL DEFAULT 'pending'");
                } catch (\Throwable $e) {
                    // Fallback if alter statement fails
                }
            }

            Schema::table('promotion_applications', function (Blueprint $table) {
                if (!Schema::hasColumn('promotion_applications', 'first_approved_by')) {
                    $table->foreignId('first_approved_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('promotion_applications', 'first_approved_at')) {
                    $table->timestamp('first_approved_at')->nullable()->after('first_approved_by');
                }
                if (!Schema::hasColumn('promotion_applications', 'first_approval_notes')) {
                    $table->text('first_approval_notes')->nullable()->after('first_approved_at');
                }
                if (!Schema::hasColumn('promotion_applications', 'final_approved_by')) {
                    $table->foreignId('final_approved_by')->nullable()->after('first_approval_notes')->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('promotion_applications', 'final_approved_at')) {
                    $table->timestamp('final_approved_at')->nullable()->after('final_approved_by');
                }
                if (!Schema::hasColumn('promotion_applications', 'final_approval_notes')) {
                    $table->text('final_approval_notes')->nullable()->after('final_approved_at');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('promotion_applications')) {
            Schema::table('promotion_applications', function (Blueprint $table) {
                $cols = [
                    'final_approval_notes',
                    'final_approved_at',
                    'final_approved_by',
                    'first_approval_notes',
                    'first_approved_at',
                    'first_approved_by'
                ];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('promotion_applications', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
