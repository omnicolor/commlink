<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Root\Http\Controllers\CharactersController;

Route::middleware('auth')
    ->prefix('characters/root')
    ->name('root.')
    ->group(function (): void {
    });

Route::get(
    '/characters/root/{character}',
    [CharactersController::class, 'view'],
)->name('root.character');
