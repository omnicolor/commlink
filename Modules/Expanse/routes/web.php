<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Expanse\Http\Controllers\CharactersController;

Route::middleware('auth')->prefix('characters')->group(function (): void {
    Route::prefix('expanse')->name('expanse.')->group(function (): void {
        Route::get('/', [CharactersController::class, 'list']);
    });
});

Route::get(
    '/characters/expanse/{character}',
    [CharactersController::class, 'view']
)->name('expanse.character');
