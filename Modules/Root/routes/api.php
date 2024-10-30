<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Root\Http\Controllers\CharactersController;

Route::middleware('auth:sanctum')
    ->prefix('root')
    ->name('root.')
    ->group(function (): void {
        Route::resource('characters', CharactersController::class)
            ->only(['index', 'show']);
    });
