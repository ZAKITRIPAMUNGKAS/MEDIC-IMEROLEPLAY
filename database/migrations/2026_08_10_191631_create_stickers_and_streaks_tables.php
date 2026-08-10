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
        // 1. Alter member_messages
        if (Schema::hasTable('member_messages')) {
            Schema::table('member_messages', function (Blueprint $table) {
                if (!Schema::hasColumn('member_messages', 'message_type')) {
                    $table->string('message_type')->default('text')->after('message');
                }
                if (!Schema::hasColumn('member_messages', 'sticker_source')) {
                    $table->string('sticker_source')->nullable()->after('message_type');
                }
                if (!Schema::hasColumn('member_messages', 'sticker_id')) {
                    $table->string('sticker_id')->nullable()->after('sticker_source');
                }
                if (!Schema::hasColumn('member_messages', 'sticker_url')) {
                    $table->text('sticker_url')->nullable()->after('sticker_id');
                }
            });
        }

        // 2. Create chat_streaks
        if (!Schema::hasTable('chat_streaks')) {
            Schema::create('chat_streaks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_one_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('user_two_id')->constrained('users')->onDelete('cascade');
                $table->integer('streak_count')->default(0);
                $table->date('last_interaction_date')->nullable();
                $table->string('status')->default('active');
                $table->timestamps();

                $table->unique(['user_one_id', 'user_two_id']);
            });
        }

        // 3. Create sticker_packs
        if (!Schema::hasTable('sticker_packs')) {
            Schema::create('sticker_packs', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('icon')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 4. Create stickers
        if (!Schema::hasTable('stickers')) {
            Schema::create('stickers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pack_id')->constrained('sticker_packs')->onDelete('cascade');
                $table->string('name')->nullable();
                $table->string('file_url');
                $table->string('file_type')->default('png');
                $table->string('keywords')->nullable();
                $table->boolean('is_animated')->default(false);
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 5. Create user_sticker_favorites
        if (!Schema::hasTable('user_sticker_favorites')) {
            Schema::create('user_sticker_favorites', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('source'); // giphy or custom
                $table->string('sticker_id');
                $table->text('sticker_url');
                $table->timestamps();

                $table->unique(['user_id', 'source', 'sticker_id']);
            });
        }

        // 6. Create user_recent_stickers
        if (!Schema::hasTable('user_recent_stickers')) {
            Schema::create('user_recent_stickers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('source'); // giphy or custom
                $table->string('sticker_id');
                $table->text('sticker_url');
                $table->timestamp('used_at');

                $table->index(['user_id', 'used_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_recent_stickers');
        Schema::dropIfExists('user_sticker_favorites');
        Schema::dropIfExists('stickers');
        Schema::dropIfExists('sticker_packs');
        Schema::dropIfExists('chat_streaks');

        if (Schema::hasTable('member_messages')) {
            Schema::table('member_messages', function (Blueprint $table) {
                $columns = [];
                if (Schema::hasColumn('member_messages', 'message_type')) {
                    $columns[] = 'message_type';
                }
                if (Schema::hasColumn('member_messages', 'sticker_source')) {
                    $columns[] = 'sticker_source';
                }
                if (Schema::hasColumn('member_messages', 'sticker_id')) {
                    $columns[] = 'sticker_id';
                }
                if (Schema::hasColumn('member_messages', 'sticker_url')) {
                    $columns[] = 'sticker_url';
                }
                if (!empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
