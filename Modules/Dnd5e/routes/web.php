<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Dnd5e\Http\Controllers\CharactersController;

Route::middleware('auth')->group(function (): void {
    Route::prefix('characters')->group(function (): void {
        Route::prefix('dnd5e')
            ->name('dnd5e.characters.list')
            ->group(function (): void {
                Route::get('/', [CharactersController::class, 'list']);
            });
    });
});

// Characters can be viewed without authorization.
Route::get(
    '/characters/dnd5e/{character}',
    [CharactersController::class, 'view']
)->name('dnd5e.character');
