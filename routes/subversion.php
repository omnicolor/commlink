<?php

declare(strict_types=1);

use App\Http\Resources\Subversion\BackgroundResource;
use App\Http\Resources\Subversion\CharacterResource;
use App\Http\Resources\Subversion\LineageResource;
use App\Http\Resources\Subversion\OriginResource;
use App\Models\Subversion\Background;
use App\Models\Subversion\Character;
use App\Models\Subversion\Lineage;
use App\Models\Subversion\Origin;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

Route::middleware('auth:sanctum')
    ->prefix('subversion')
    ->name('subversion.')
    ->group(function (): void {
        Route::get('backgrounds', function (): AnonymousResourceCollection {
            return BackgroundResource::collection(Background::all());
        })->name('backgrounds.index');
        Route::get(
            'backgrounds/{background}',
            function (string $background): BackgroundResource {
                return new BackgroundResource(new Background($background));
            }
        )->name('backgrounds.show');

        Route::get(
            'characters',
            function (Request $request): AnonymousResourceCollection {
                return CharacterResource::collection(
                    $request->user()
                        ->characters('subversion')
                        ->get()
                )
                    ->additional([
                        'links' => [
                            'self' => route('subversion.characters.index'),
                        ],
                    ]);
            }
        )->name('characters.index');
        Route::get(
            'characters/{character}',
            function (Character $character): CharacterResource {
                return new CharacterResource($character);
            }
        )->name('characters.show');

        Route::get('lineages', function (): AnonymousResourceCollection {
            return LineageResource::collection(Lineage::all());
        })->name('lineages.index');
        Route::get(
            'lineages/{lineage}',
            function (string $lineage): LineageResource {
                return new LineageResource(new Lineage($lineage));
            }
        )->name('lineages.show');

        Route::get('origins', function (): AnonymousResourceCollection {
            return OriginResource::collection(Origin::all());
        })->name('origins.index');
        Route::get('origins/{origin}', function (string $origin): OriginResource {
            return new OriginResource(new Origin($origin));
        })->name('origins.show');
    });
