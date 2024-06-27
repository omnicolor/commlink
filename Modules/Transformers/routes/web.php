<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Transformers\Http\Controllers\CharactersController;

Route::middleware('auth')->group(function (): void {
    Route::prefix('characters/transformers')->name('transformers.')->group(function (): void {
        Route::get(
            'create/{step?}',
            [CharactersController::class, 'create'],
        )->name('create');

        Route::post(
            'create/base',
            [CharactersController::class, 'createBase'],
        )->name('create-base');
        Route::post(
            'create/statistics',
            [CharactersController::class, 'createStatistics'],
        )->name('create-statistics');
        Route::post(
            'create/function',
            [CharactersController::class, 'createProgramming'],
        )->name('create-programming');
    });
});

Route::group([], function (): void {

    Route::get(
        '/characters/transformers/{character}',
        [CharactersController::class, 'view']
    )->name('transformers.character');
});
