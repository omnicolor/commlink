<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Battletech\Http\Controllers\CharactersController;

Route::name('battletech.')
    ->group(function (): void {
        Route::get(
            '/characters/battletech/{character}',
            [CharactersController::class, 'view']
        )->name('character');
    });
