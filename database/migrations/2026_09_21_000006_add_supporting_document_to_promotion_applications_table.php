<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('promotion_applications')) {
            Schema::table('promotion_applications', function (Blueprint $table) {
                if (!Schema::hasColumn('promotion_applications', 'supporting_document')) {
                    $table->string('supporting_document')->nullable()->after('recommendation_letter_2');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('promotion_applications')) {
            Schema::table('promotion_applications', function (Blueprint $table) {
                if (Schema::hasColumn('promotion_applications', 'supporting_document')) {
                    $table->dropColumn('supporting_document');
                }
            });
        }
    }
};
