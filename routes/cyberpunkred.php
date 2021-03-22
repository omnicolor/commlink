<?php

declare(strict_types=1);

use App\Http\Controllers\CyberpunkRed\CharactersController;

Route::middleware('auth:sanctum')->prefix('cyberpunkred')->name('cyberpunkred.')->group(
    function (): void {
        Route::resource('characters', CharactersController::class)
            ->only(['index', 'show']);
    }
);
