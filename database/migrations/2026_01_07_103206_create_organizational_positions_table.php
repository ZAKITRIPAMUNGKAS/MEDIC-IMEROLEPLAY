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
        Schema::create('organizational_positions', function (Blueprint $table) {
            $table->id();
            $table->integer('level')->comment('Hierarchy level: 0=High Command, 1=Department, etc');
            $table->string('level_key', 50)->nullable()->comment('level_0, level_1, etc for styling');
            $table->foreignId('parent_id')->nullable()->constrained('organizational_positions')->onDelete('cascade');
            $table->string('title')->comment('Position title: CEO, Department Head, etc');
            $table->string('position_name')->nullable()->comment('Full position name or department name');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('display_order')->default(0)->comment('Display order within same level');
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable()->comment('Extra data: icon, color, description, etc');
            $table->timestamps();

            // Indexes for performance
            $table->index(['level', 'display_order']);
            $table->index('parent_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizational_positions');
    }
};
