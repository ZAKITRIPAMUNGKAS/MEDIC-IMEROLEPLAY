<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_forms', function (Blueprint $table) {
            if (!Schema::hasColumn('medical_forms', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('notes');
            }
            DB::statement("ALTER TABLE medical_forms MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'cancelled') DEFAULT 'pending'");
        });

        Schema::table('payrolls', function (Blueprint $table) {
            if (!Schema::hasColumn('payrolls', 'cancel_reason')) {
                $table->text('cancel_reason')->nullable()->after('status');
            }
            DB::statement("ALTER TABLE payrolls MODIFY COLUMN status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending'");
        });
    }

    public function down(): void
    {
        Schema::table('medical_forms', function (Blueprint $table) {
            if (Schema::hasColumn('medical_forms', 'cancelled_at')) {
                $table->dropColumn('cancelled_at');
            }
            DB::statement("ALTER TABLE medical_forms MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
        });

        Schema::table('payrolls', function (Blueprint $table) {
            if (Schema::hasColumn('payrolls', 'cancel_reason')) {
                $table->dropColumn('cancel_reason');
            }
            DB::statement("ALTER TABLE payrolls MODIFY COLUMN status ENUM('pending', 'paid') DEFAULT 'pending'");
        });
    }
};
