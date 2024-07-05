<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Capers\Http\Controllers\CharactersController;

Route::middleware('auth')->prefix('characters')->group(function (): void {
    Route::prefix('capers')->name('capers.')->group(function (): void {
        Route::get(
            'create/{step?}',
            [CharactersController::class, 'create']
        )->name('create');

        Route::post(
            'create/anchors',
            [CharactersController::class, 'storeAnchors']
        )->name('create-anchors');
        Route::post(
            'create/basics',
            [CharactersController::class, 'storeBasics']
        )->name('create-basics');
        Route::post(
            'create/boosts',
            [CharactersController::class, 'storeBoosts']
        )->name('create-boosts');
        Route::post(
            'create/gear',
            [CharactersController::class, 'storeGear']
        )->name('create-gear');
        Route::post(
            'create/powers',
            [CharactersController::class, 'storePowers']
        )->name('create-powers');
        Route::post(
            'create/save',
            [CharactersController::class, 'saveCharacter']
        )->name('create-save');
        Route::post(
            'create/skills',
            [CharactersController::class, 'storeSkills']
        )->name('create-skills');
        Route::post(
            'create/traits',
            [CharactersController::class, 'storeTraits']
        )->name('create-traits');
    });
});

// Allow character sheets to be viewed without being logged in.
Route::get(
    '/characters/capers/{character}',
    [CharactersController::class, 'view']
)->name('capers.character');
