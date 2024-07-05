<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Transformers\Http\Controllers\CharactersController;

Route::middleware(['auth:sanctum'])
    ->prefix('transformers')
    ->name('transformers.')
    ->group(function (): void {
        Route::resource('characters', CharactersController::class)
            ->only(['index', 'show']);
    });
