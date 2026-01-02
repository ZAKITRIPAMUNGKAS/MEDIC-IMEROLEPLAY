<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // Name if guest, or override for auth users
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // If logged in
            $table->enum('type', ['kritik', 'saran']); // Criticism or Suggestion
            $table->string('subject'); // Subject/title
            $table->text('message'); // Detailed feedback
            $table->enum('status', ['new', 'reviewed', 'resolved'])->default('new');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete(); // Staff who reviewed
            $table->timestamp('reviewed_at')->nullable();
            $table->text('notes')->nullable(); // Admin notes
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('feedback');
    }
};
