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
        Schema::create('votings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('target_position')->nullable(); // e.g. "Wakil Direktur", "Kadiv Medis"
            $table->text('description')->nullable();
            $table->enum('hospital', ['alta', 'roxwood', 'all'])->default('all');
            $table->enum('status', ['draft', 'active', 'closed'])->default('draft');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('voting_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voting_id')->constrained('votings')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('name');
            $table->string('custom_role')->nullable(); // e.g. "Dokter Spesialis"
            $table->text('vision_mission')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
        });

        Schema::create('voting_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voting_id')->constrained('votings')->onDelete('cascade');
            $table->foreignId('candidate_id')->constrained('voting_candidates')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['voting_id', 'user_id'], 'unique_user_vote_per_session');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voting_votes');
        Schema::dropIfExists('voting_candidates');
        Schema::dropIfExists('votings');
    }
};
