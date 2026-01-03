<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            // Default true because initially there are no unread replies from staff
            // When staff replies, we set this to false
            $table->boolean('is_user_read')->default(true)->after('is_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->dropColumn('is_user_read');
        });
    }
};
