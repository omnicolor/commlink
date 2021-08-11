<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeCampaignUserAcceptedToStatus extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('campaign_user', function (Blueprint $table): void {
            $table->dropColumn('accepted');
            $table->enum('status', ['invited', 'accepted', 'banned', 'removed'])
                ->after('campaign_id')
                ->default('invited');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaign_user', function (Blueprint $table): void {
            $table->boolean('accepted')->default(false)->after('campaign_id');
            $table->dropColumn('status');
        });
    }
}
