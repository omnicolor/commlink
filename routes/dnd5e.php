<?php

declare(strict_types=1);

use App\Http\Controllers\Dnd5e\CharactersController;

Route::middleware('auth:sanctum')->prefix('dnd5e')->name('dnd5e.')->group(
    function (): void {
        Route::resource('characters', CharactersController::class)
            ->only(['index', 'show']);
    }
);
