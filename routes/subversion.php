<?php

declare(strict_types=1);

use App\Http\Resources\Subversion\LineageResource;
use App\Models\Subversion\Lineage;

Route::middleware('auth:sanctum')
    ->prefix('subversion')
    ->name('subversion.')
    ->group(function (): void {
        Route::get('lineages', function () {
            return LineageResource::collection(Lineage::all());
        })->name('lineages.index');
        Route::get('lineages/{lineage}', function (string $lineage): LineageResource {
            return new LineageResource(new Lineage($lineage));
        })->name('lineages.show');
    });
