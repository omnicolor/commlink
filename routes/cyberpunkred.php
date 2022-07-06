<?php

declare(strict_types=1);

use App\Http\Controllers\Cyberpunkred\CharactersController;

Route::middleware('auth:sanctum')->prefix('cyberpunkred')->name('cyberpunkred.')->group(
    function (): void {
        Route::resource('characters', CharactersController::class)
            ->only(['index', 'show']);
    }
);
