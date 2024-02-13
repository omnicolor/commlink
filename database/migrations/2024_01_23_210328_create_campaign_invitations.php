<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create(
            'campaign_invitations',
            function (Blueprint $table): void {
                $table->id();
                // Not unique: You might have multiple different GMs invite a
                // user to their tables.
                $table->string('email');
                $table->string('name');
                $table->foreignId('invited_by')->references('id')->on('users');
                $table->foreignId('campaign_id')->constrained();
                $table->enum('status', ['invited', 'responded', 'spam'])
                      ->default('invited');
                $table->timestamp('responded_at')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable()->default(null);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_invitations');
    }
};
