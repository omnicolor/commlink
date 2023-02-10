<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddIrcToChatUsers extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite can't update enums, so for SQLite connections we've already
        // done the relevant migration. See
        // 2021_03_31_210141_create_chat_app_links.php for where that happened.
        if ('sqlite' === Schema::getConnection()->getName()) {
            return;
        }
        DB::statement(
            'ALTER TABLE chat_users MODIFY COLUMN server_type ENUM("slack", "discord", "irc")'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(
            'ALTER TABLE chat_users MODIFY COLUMN server_type ENUM("slack", "discord")'
        );
    }
}
