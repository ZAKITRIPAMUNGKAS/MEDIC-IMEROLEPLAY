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
        if (!Schema::hasTable('training_programs')) {
            Schema::create('training_programs', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->string('title');
                $table->string('short_title')->nullable();
                $table->string('organizer')->nullable();
                $table->text('desc')->nullable();
                $table->string('requirement')->nullable();
                $table->string('badge')->nullable();
                $table->string('badge_color')->default('emerald');
                $table->string('icon')->default('fa-graduation-cap');
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_programs');
    }
};
