<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Shadowrun5e\Http\Controllers\CharactersController;

Route::middleware('auth')->prefix('characters')->group(function (): void {
    Route::prefix('shadowrun5e')
        ->name('shadowrun5e.')
        ->group(function (): void {
            Route::get('/', [CharactersController::class, 'list']);
            Route::get(
                'create/save-for-later',
                [CharactersController::class, 'saveForLater'],
            );
            Route::get(
                'create/{step?}',
                [CharactersController::class, 'create'],
            );
            Route::post(
                'create/attributes',
                [CharactersController::class, 'storeAttributes'],
            )->name('create-attributes');
            Route::post(
                'create/background',
                [CharactersController::class, 'storeBackground'],
            )->name('create-background');
            Route::post(
                'create/knowledge',
                [CharactersController::class, 'storeKnowledgeSkills'],
            )->name('create-knowledge-skills');
            Route::post(
                'create/martial-arts',
                [CharactersController::class, 'storeMartialArts'],
            )->name('create-martial-arts');
            Route::post(
                'create/qualities',
                [CharactersController::class, 'storeQualities'],
            )->name('create-qualities');
            Route::post(
                'create/rules',
                [CharactersController::class, 'storeRules'],
            )->name('create-rules');
            Route::post(
                'create/skills',
                [CharactersController::class, 'storeSkills'],
            )->name('create-skills');
            Route::post(
                'create/social',
                [CharactersController::class, 'storeSocial'],
            )->name('create-social');
            Route::post(
                'create/standard',
                [CharactersController::class, 'storeStandard'],
            )->name('create-standard');
            Route::post(
                'create/vitals',
                [CharactersController::class, 'storeVitals'],
            )->name('create-vitals');
        });
});

Route::get(
    '/characters/shadowrun5e/{character}',
    [CharactersController::class, 'view']
)->name('shadowrun5e.character');
