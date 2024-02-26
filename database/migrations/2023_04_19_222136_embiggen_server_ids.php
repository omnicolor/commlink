<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EmbiggenServerIds extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('channels', function (Blueprint $table): void {
            $table->string('server_id', 255)->change();
        });
        Schema::table('chat_users', function (Blueprint $table): void {
            $table->string('server_id', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('channels', function (Blueprint $table): void {
            $table->string('server_id', 20)->change();
        });
        Schema::table('chat_users', function (Blueprint $table): void {
            $table->string('server_id', 20)->change();
        });
    }
}
