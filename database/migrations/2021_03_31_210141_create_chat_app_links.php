<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create the tables for allowing users to link to their Slack and Discord
 * users.
 */
class CreateChatAppLinks extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('channels', function (Blueprint $table): void {
            $table->id();
            $table->string('server_id', 20);
            $table->string('server_name', 50)->nullable(true);
            $table->string('channel_id', 20);
            $table->string('channel_name', 50)->nullable(true);
            $table->foreignId('registered_by')->references('id')->on('users');
            $table->string('system', 30);
            $table->enum('type', ['slack', 'discord']);
            $table->timestamps();
        });
        Schema::create('chat_users', function (Blueprint $table): void {
            $table->id();
            $table->string('server_id', 20);
            $table->string('server_name', 50)->nullable(true);
            $table->enum('server_type', ['slack', 'discord']);
            $table->string('remote_user_id', 50);
            $table->string('remote_user_name', 50)->nullable(true);
            $table->foreignId('user_id')->constrained();
            $table->boolean('verified')->default(false);
            $table->timestamps();
        });
        Schema::create('chat_characters', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('channel_id')->constrained();
            $table->foreignId('chat_user_id')->constrained();
            $table->char('character_id', 24);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_characters');
        Schema::dropIfExists('chat_users');
        Schema::dropIfExists('channels');
    }
}
