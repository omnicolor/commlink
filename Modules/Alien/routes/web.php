<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Alien\Http\Controllers\CharactersController;

Route::middleware('auth')
    ->prefix('characters/alien')
    ->name('alien.')
    ->group(function (): void {
        Route::get(
            'create/{step?}',
            [CharactersController::class, 'create']
        )->name('create');

        Route::post(
            'create/attributes',
            [CharactersController::class, 'saveAttributes'],
        )->name('save-attributes');

        Route::post(
            'create/career',
            [CharactersController::class, 'saveCareer'],
        )->name('save-career');

        Route::post(
            'create/finish',
            [CharactersController::class, 'saveFinish'],
        )->name('save-finish');

        Route::post(
            'create/gear',
            [CharactersController::class, 'saveGear'],
        )->name('save-gear');

        Route::post(
            'create/save',
            [CharactersController::class, 'saveCharacter'],
        )->name('save-character');

        Route::post(
            'create/skills',
            [CharactersController::class, 'saveSkills'],
        )->name('save-skills');

        Route::post(
            'create/talent',
            [CharactersController::class, 'saveTalent'],
        )->name('save-talent');
    });

Route::get(
    '/characters/alien/{character}',
    [CharactersController::class, 'view'],
)->name('alien.character');
