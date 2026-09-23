<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Tambahkan kolom email dan password_temp ke recruitment_applications
     * agar akun dapat dibuat otomatis saat pelamar diterima.
     */
    public function up(): void
    {
        if (Schema::hasTable('recruitment_applications')) {
            Schema::table('recruitment_applications', function (Blueprint $table) {
                if (!Schema::hasColumn('recruitment_applications', 'email')) {
                    $table->string('email')->nullable()->after('discord_username');
                }
                if (!Schema::hasColumn('recruitment_applications', 'password_temp')) {
                    $table->string('password_temp')->nullable()->after('email');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('recruitment_applications')) {
            Schema::table('recruitment_applications', function (Blueprint $table) {
                $table->dropColumn(['email', 'password_temp']);
            });
        }
    }
};
