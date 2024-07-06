<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Avatar\Http\Controllers\CharactersController;

Route::middleware(['auth:sanctum'])
    ->prefix('avatar')
    ->name('avatar.')
    ->group(
        function (): void {
            Route::resource('characters', CharactersController::class)
                ->only(['index', 'show']);
        }
    );
