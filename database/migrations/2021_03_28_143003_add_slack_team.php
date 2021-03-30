<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlackTeam extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('slack_links', function (Blueprint $table): void {
            $table->string('team_name', 100)
                ->after('slack_team')
                ->nullable(true);
            $table->string('user_name', 100)
                ->after('slack_user')
                ->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slack_links', function (Blueprint $table): void {
            $table->dropColumn('team_name');
            $table->dropColumn('user_name');
        });
    }
}
