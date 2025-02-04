<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        if ('sqlite' === config('database.default')) {
            return;
        }
        Schema::table('chat_characters', function (Blueprint $table): void {
            $table->dropForeign('chat_characters_channel_id_foreign');
            $table->foreign('channel_id')
                ->references('id')
                ->on('channels')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        if ('sqlite' === config('database.default')) {
            return;
        }
        Schema::table('chat_characters', function (Blueprint $table): void {
            $table->dropForeign('chat_characters_channel_id_foreign');
            $table->foreign('channel_id')
                ->references('id')
                ->on('channels');
        });
    }
};
