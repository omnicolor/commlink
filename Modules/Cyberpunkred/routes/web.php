<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Cyberpunkred\Http\Controllers\CharactersController;

Route::middleware('auth')->prefix('characters')->group(function (): void {
    Route::prefix('cyberpunkred')->name('cyberpunkred.')->group(function (): void {
        Route::get(
            'create/{step?}',
            [CharactersController::class, 'createForm'],
        )->name('create');
        Route::post(
            'create/role',
            [CharactersController::class, 'storeRole'],
        )->name('create-role');
        Route::post(
            'create/handle',
            [CharactersController::class, 'storeHandle'],
        )->name('create-handle');
        Route::post(
            'create/lifepath',
            [CharactersController::class, 'storeLifepath'],
        )->name('create-lifepath');
        Route::post(
            'create/stats',
            [CharactersController::class, 'storeStats'],
        )->name('create-stats');
    });
});

Route::get(
    '/characters/cyberpunkred/{character}',
    [CharactersController::class, 'view']
)->name('cyberpunkred.character');
