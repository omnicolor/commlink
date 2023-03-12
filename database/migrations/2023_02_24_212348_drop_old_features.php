<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Moving from Juststeveking's feature flags to Pennant.
 */
class DropOldFeatures extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('feature_group_user');
        Schema::dropIfExists('feature_feature_group');
        Schema::dropIfExists('feature_user');
        Schema::dropIfExists('feature_groups');
        Schema::dropIfExists('features');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /*
        Schema::create('features', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        Schema::create('feature_groups', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        Schema::create('feature_feature_group', function (Blueprint $table): void {
            $table->unsignedBigInteger('feature_id');
            $table->unsignedBigInteger('feature_group_id');

            $table->foreign('feature_id')->references('id')->on('features')->onDelete('CASCADE');
            $table->foreign('feature_group_id')->references('id')->on('feature_groups')->onDelete('CASCADE');

            $table->primary(['feature_id', 'feature_group_id']);
        });
        Schema::create('feature_user', function (Blueprint $table): void {
            $table->unsignedBigInteger('feature_id');
            $table->unsignedBigInteger('user_id');

            $table->foreign('feature_id')->references('id')->on('features')->onDelete('CASCADE');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');

            $table->primary(['feature_id', 'user_id']);
        });
        Schema::create('feature_group_user', function (Blueprint $table): void {
            $table->unsignedBigInteger('feature_group_id');
            $table->unsignedBigInteger('user_id');

            $table->foreign('feature_group_id')->references('id')->on('feature_groups')->onDelete('CASCADE');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');

            $table->primary(['feature_group_id', 'user_id']);
        });
         */
    }
}
