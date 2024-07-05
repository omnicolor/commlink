<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Blistercritters\Http\Controllers\CharactersController;

Route::middleware(['auth:sanctum'])
    ->prefix('blistercritters')
    ->name('blistercritters.')
    ->group(
        function (): void {
            Route::resource('characters', CharactersController::class)
                ->only(['index', 'show']);
        }
    );
