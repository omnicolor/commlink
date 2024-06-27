<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Blistercritters\Http\Controllers\CharactersController;

Route::group([], function (): void {
    Route::get(
        '/characters/blistercritters/{character}',
        [CharactersController::class, 'view']
    )->name('blistercritters.character');
});
