<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeatureGroupsTable extends Migration
{
    public function up(): void
    {
        /*
        Schema::create('feature_groups', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
         */
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_groups');
    }
}
