<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('campaign_id')->constrained();
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
            $table->dateTimeTz('real_start');
            $table->dateTimeTz('real_end')->nullable();
            $table->string('game_start')->nullable();
            $table->string('game_end')->nullable();
            $table->softDeletesTz();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate();
        });

        Schema::create('event_rsvps', function (Blueprint $table): void {
            $table->id();
            $table->enum('response', ['accepted', 'declined', 'tentative']);
            $table->foreignId('event_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('event_rsvps');
        Schema::drop('events');
    }
};
