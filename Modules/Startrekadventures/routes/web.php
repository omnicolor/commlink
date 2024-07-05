<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Startrekadventures\Http\Controllers\CharactersController;

Route::middleware('auth')->group(function (): void {
    Route::prefix('characters')->group(function (): void {
        Route::prefix('startrekadventures')
            ->name('startrekadventures.characters.list')
            ->group(function (): void {
                Route::get('/', [CharactersController::class, 'list']);
            });
    });
});

// Characters can be viewed without authorization.
Route::get(
    '/characters/startrekadventures/{character}',
    [CharactersController::class, 'view']
)->name('startrekadventures.character');
