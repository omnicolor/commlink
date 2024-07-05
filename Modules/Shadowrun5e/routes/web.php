<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Shadowrun5e\Http\Controllers\CharactersController as ShadowrunController;

Route::middleware('auth')->prefix('characters')->group(function (): void {
    Route::prefix('shadowrun5e')
        ->name('shadowrun5e.')
        ->group(function (): void {
            Route::get('/', [ShadowrunController::class, 'list']);
            Route::get(
                'create/{step?}',
                [ShadowrunController::class, 'create'],
            );
            Route::post(
                'create/attributes',
                [ShadowrunController::class, 'storeAttributes'],
            )->name('create-attributes');
            Route::post(
                'create/background',
                [ShadowrunController::class, 'storeBackground'],
            )->name('create-background');
            Route::post(
                'create/knowledge',
                [ShadowrunController::class, 'storeKnowledgeSkills'],
            )->name('create-knowledge-skills');
            Route::post(
                'create/martial-arts',
                [ShadowrunController::class, 'storeMartialArts'],
            )->name('create-martial-arts');
            Route::post(
                'create/qualities',
                [ShadowrunController::class, 'storeQualities'],
            )->name('create-qualities');
            Route::post(
                'create/rules',
                [ShadowrunController::class, 'storeRules'],
            )->name('create-rules');
            Route::post(
                'create/skills',
                [ShadowrunController::class, 'storeSkills'],
            )->name('create-skills');
            Route::post(
                'create/standard',
                [ShadowrunController::class, 'storeStandard'],
            )->name('create-standard');
            Route::post(
                'create/vitals',
                [ShadowrunController::class, 'storeVitals'],
            )->name('create-vitals');
        });
});

Route::get(
    '/characters/shadowrun5e/{character}',
    [ShadowrunController::class, 'view']
)->name('shadowrun5e.character');
