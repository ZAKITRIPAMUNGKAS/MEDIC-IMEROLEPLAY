<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom weight di voting_votes
        Schema::table('voting_votes', function (Blueprint $table) {
            $table->unsignedTinyInteger('weight')->default(1)->after('user_id')
                  ->comment('Bobot suara voter: 1=staf biasa, 4=staff_manager, 5=manajer/executive/admin');
        });

        // Tambah kolom audience di votings
        Schema::table('votings', function (Blueprint $table) {
            $table->string('audience')->default('all')->after('status')
                  ->comment('all=semua staf, management=hanya staff_manager ke atas');
        });
    }

    public function down(): void
    {
        Schema::table('voting_votes', function (Blueprint $table) {
            $table->dropColumn('weight');
        });

        Schema::table('votings', function (Blueprint $table) {
            $table->dropColumn('audience');
        });
    }
};
