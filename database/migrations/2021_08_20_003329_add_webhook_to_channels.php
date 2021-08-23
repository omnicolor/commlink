<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWebhookToChannels extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('channels', function (Blueprint $table): void {
            $table->string('webhook', 255)
                ->nullable()
                ->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('channels', function (Blueprint $table): void {
            $table->dropColumn('webhook');
        });
    }
}
