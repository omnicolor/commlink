<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('snapshots', static function (Blueprint $table): void {
            $table->id();
            $table->uuid('aggregate_uuid');
            $table->unsignedBigInteger('aggregate_version');
            $table->jsonb('state');

            $table->timestamps();

            $table->index('aggregate_uuid');
        });
    }
};
