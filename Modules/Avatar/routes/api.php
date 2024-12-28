<?php

declare(strict_types=1);

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Route;
use Modules\Avatar\Http\Controllers\CharactersController;
use Modules\Avatar\Http\Resources\MoveResource;
use Modules\Avatar\Http\Resources\PlaybookResource;
use Modules\Avatar\Http\Resources\StatusResource;
use Modules\Avatar\Models\Move;
use Modules\Avatar\Models\Playbook;
use Modules\Avatar\Models\Status;

Route::middleware(['auth:sanctum'])
    ->prefix('avatar')
    ->name('avatar.')
    ->group(function (): void {
        Route::resource('characters', CharactersController::class)
            ->only(['index', 'show']);

        Route::get('moves', function (): AnonymousResourceCollection {
            return MoveResource::collection(Move::all());
        })->name('moves.index');
        Route::get(
            'moves/{move}',
            function (string $move): MoveResource {
                return new MoveResource(new Move($move));
            },
        )->name('moves.show');

        Route::get('playbooks', function (): AnonymousResourceCollection {
            return PlaybookResource::collection(Playbook::all());
        })->name('playbooks.index');
        Route::get(
            'playbooks/{playbook}',
            function (string $playbook): PlaybookResource {
                return new PlaybookResource(new Playbook($playbook));
            },
        )->name('playbooks.show');

        Route::get('statuses', function (): AnonymousResourceCollection {
            return StatusResource::collection(Status::all());
        })->name('statuses.index');
        Route::get(
            'statuses/{status}',
            function (string $status): StatusResource {
                return new StatusResource(new Status($status));
            },
        )->name('statuses.show');
    });
