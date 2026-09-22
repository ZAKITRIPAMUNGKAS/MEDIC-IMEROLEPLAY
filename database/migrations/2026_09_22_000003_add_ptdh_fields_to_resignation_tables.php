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
        if (Schema::hasTable('resignation_requests')) {
            Schema::table('resignation_requests', function (Blueprint $table) {
                if (!Schema::hasColumn('resignation_requests', 'type')) {
                    $table->string('type', 20)->default('resignation')->after('user_id');
                }
                if (!Schema::hasColumn('resignation_requests', 'ptdh_additional_fee')) {
                    $table->unsignedBigInteger('ptdh_additional_fee')->default(0)->after('fine_percentage');
                }
            });
        }

        if (Schema::hasTable('resignation_logs')) {
            Schema::table('resignation_logs', function (Blueprint $table) {
                if (!Schema::hasColumn('resignation_logs', 'type')) {
                    $table->string('type', 20)->default('resignation')->after('user_id');
                }
                if (!Schema::hasColumn('resignation_logs', 'ptdh_additional_fee')) {
                    $table->unsignedBigInteger('ptdh_additional_fee')->default(0)->after('fine_percentage');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('resignation_requests')) {
            Schema::table('resignation_requests', function (Blueprint $table) {
                if (Schema::hasColumn('resignation_requests', 'ptdh_additional_fee')) {
                    $table->dropColumn('ptdh_additional_fee');
                }
                if (Schema::hasColumn('resignation_requests', 'type')) {
                    $table->dropColumn('type');
                }
            });
        }

        if (Schema::hasTable('resignation_logs')) {
            Schema::table('resignation_logs', function (Blueprint $table) {
                if (Schema::hasColumn('resignation_logs', 'ptdh_additional_fee')) {
                    $table->dropColumn('ptdh_additional_fee');
                }
                if (Schema::hasColumn('resignation_logs', 'type')) {
                    $table->dropColumn('type');
                }
            });
        }
    }
};
