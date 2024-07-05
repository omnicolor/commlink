<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Cyberpunkred\Http\Controllers\CharactersController;

Route::middleware('auth:sanctum')
    ->prefix('cyberpunkred')
    ->name('cyberpunkred.')
    ->group(function (): void {
        Route::resource('characters', CharactersController::class)
            ->only(['index', 'show']);
    });
