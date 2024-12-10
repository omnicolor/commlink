<?php

declare(strict_types=1);

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Route;
use Modules\Root\Http\Controllers\CharactersController;
use Modules\Root\Http\Resources\MoveResource;
use Modules\Root\Http\Resources\NatureResource;
use Modules\Root\Http\Resources\PlaybookResource;
use Modules\Root\Models\Move;
use Modules\Root\Models\Nature;
use Modules\Root\Models\Playbook;

Route::middleware('auth:sanctum')
    ->prefix('root')
    ->name('root.')
    ->group(function (): void {
        Route::resource('characters', CharactersController::class)
            ->only(['index', 'show']);

        Route::get('moves', function (): AnonymousResourceCollection {
            return MoveResource::collection(Move::all());
        })->name('moves.index');
        Route::get('moves/{move}', function (Move $move): MoveResource {
            return new MoveResource($move);
        })->name('moves.show');

        Route::get('natures', function (): AnonymousResourceCollection {
            return NatureResource::collection(Nature::all());
        })->name('natures.index');
        Route::get(
            'natures/{nature}',
            function (Nature $nature): NatureResource {
                return new NatureResource($nature);
            }
        )->name('natures.show');

        Route::get('playbooks', function (): AnonymousResourceCollection {
            return PlaybookResource::collection(Playbook::all());
        })->name('playbooks.index');
        Route::get(
            'playbooks/{playbook}',
            function (Playbook $playbook): PlaybookResource {
                return new PlaybookResource($playbook);
            }
        )->name('playbooks.show');
    });
