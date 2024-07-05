<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Avatar\Http\Controllers\CharactersController;

Route::group([], function (): void {
    Route::get(
        '/characters/avatar/{character}',
        [CharactersController::class, 'view'],
    )->name('avatar.character');
});
