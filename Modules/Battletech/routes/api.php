<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Battletech\Http\Controllers\CharactersController;

Route::middleware(['auth:sanctum'])
    ->prefix('battletech')
    ->name('battletech.')
    ->group(function (): void {
        Route::resource('characters', CharactersController::class)
            ->only(['index', 'show']);
    });
