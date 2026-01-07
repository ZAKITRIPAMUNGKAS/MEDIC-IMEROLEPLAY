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
        Schema::create('organizational_structures', function (Blueprint $table) {
            $table->id();
            $table->json('structure_data'); // Stores the entire hierarchy structure
            $table->json('required_names')->nullable(); // Stores the required names list
            $table->enum('hospital_type', ['ems', 'roxwood'])->default('ems');
            $table->boolean('is_active')->default(false);
            $table->string('name')->nullable(); // Optional name/label for the structure
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizational_structures');
    }
};
