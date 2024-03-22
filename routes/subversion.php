<?php

declare(strict_types=1);

use App\Http\Resources\Subversion\LineageResource;
use App\Http\Resources\Subversion\OriginResource;
use App\Models\Subversion\Lineage;
use App\Models\Subversion\Origin;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

Route::middleware('auth:sanctum')
    ->prefix('subversion')
    ->name('subversion.')
    ->group(function (): void {
        Route::get('lineages', function (): AnonymousResourceCollection {
            return LineageResource::collection(Lineage::all());
        })->name('lineages.index');
        Route::get('lineages/{lineage}', function (string $lineage): LineageResource {
            return new LineageResource(new Lineage($lineage));
        })->name('lineages.show');

        Route::get('origins', function (): AnonymousResourceCollection {
            return OriginResource::collection(Origin::all());
        })->name('origins.index');
        Route::get('origins/{origin}', function (string $origin): OriginResource {
            return new OriginResource(new Origin($origin));
        })->name('origins.show');
    });
