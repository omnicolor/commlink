<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeatureFeatureGroupTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('feature_feature_group', function (Blueprint $table): void {
            $table->unsignedBigInteger('feature_id');
            $table->unsignedBigInteger('feature_group_id');

            $table->foreign('feature_id')->references('id')->on('features')->onDelete('CASCADE');
            $table->foreign('feature_group_id')->references('id')->on('feature_groups')->onDelete('CASCADE');

            $table->primary(['feature_id', 'feature_group_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_feature_group');
    }
}
