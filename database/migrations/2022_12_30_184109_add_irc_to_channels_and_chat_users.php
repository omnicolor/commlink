<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddIrcToChannelsAndChatUsers extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('channels', function (Blueprint $table): void {
            $table->string('type', 20)->change();
            $table->renameColumn('type', 'type_string');
            $table->enum('type', ['slack', 'discord', 'irc']);
            DB::table('channels')->update(['type' => 'type_string']);
            $table->dropColumn('type_string');
        });
        /*
        Schema::table('chat_users', function (Blueprint $table): void {
            //$table->enum('server_type', ['slack', 'discord', 'irc'])->change();
        });
         */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE channels CHANGE type type ENUM("slack", "discord")');
        /*
        Schema::table('channels', function (Blueprint $table): void {
            //$table->dropColumn('type_backup');
            $table->enum('type', ['slack', 'discord'])->change();
        });
        Schema::table('chat_users', function (Blueprint $table): void {
            //$table->enum('server_type', ['slack', 'discord'])->change();
        });
         */
    }
}
