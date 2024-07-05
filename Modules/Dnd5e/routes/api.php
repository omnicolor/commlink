<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Dnd5e\Http\Controllers\CharactersController;

Route::middleware('auth:sanctum')
    ->prefix('dnd5e')
    ->name('dnd5e.')->group(function (): void {
        Route::resource('characters', CharactersController::class)
            ->only(['index', 'show']);
    });
