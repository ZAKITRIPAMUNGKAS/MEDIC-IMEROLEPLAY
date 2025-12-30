<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_token')->unique(); // For guest identification
            $table->string('name')->nullable(); // Guest name / Character name
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // If logged in
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->boolean('is_read')->default(false); // For admin notification
            $table->timestamps();
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Sender (if staff)
            $table->text('message');
            $table->boolean('is_staff_reply')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_sessions');
    }
};
