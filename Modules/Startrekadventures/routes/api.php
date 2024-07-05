<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Startrekadventures\Http\Controllers\CharactersController;

Route::middleware(['auth:sanctum'])
    ->prefix('startrekadventures')
    ->name('startrekadventures.')
    ->group(function (): void {
        Route::resource('characters', CharactersController::class)
            ->only(['index', 'show']);
    });
