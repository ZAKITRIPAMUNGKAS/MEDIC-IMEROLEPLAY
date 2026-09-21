<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * Ubah status menjadi VARCHAR(50) agar mendukung status 'cancelled'
     * dan status lainnya tanpa batasan kaku MySQL ENUM.
     */
    public function up(): void
    {
        if (Schema::hasTable('resignation_requests')) {
            DB::statement("ALTER TABLE resignation_requests MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'pending_pnd'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('resignation_requests')) {
            DB::statement("ALTER TABLE resignation_requests MODIFY COLUMN status ENUM('pending_pnd', 'approved_pnd', 'pending_ie', 'completed', 'rejected') NOT NULL DEFAULT 'pending_pnd'");
        }
    }
};
