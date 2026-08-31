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
        Schema::table('feedback', function (Blueprint $table) {
            if (!Schema::hasColumn('feedback', 'hospital')) {
                $table->string('hospital')->default('alta')->after('user_id'); // alta, roxwood
            }
            if (!Schema::hasColumn('feedback', 'reporter_type')) {
                $table->string('reporter_type')->default('warga')->after('hospital'); // warga, medic
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            if (Schema::hasColumn('feedback', 'hospital')) {
                $table->dropColumn('hospital');
            }
            if (Schema::hasColumn('feedback', 'reporter_type')) {
                $table->dropColumn('reporter_type');
            }
        });
    }
};
