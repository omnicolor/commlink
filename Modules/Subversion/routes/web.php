<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Subversion\Http\Controllers\CharactersController;

Route::middleware('auth')->prefix('characters')->group(function (): void {
    Route::prefix('subversion')
        ->name('subversion.')
        ->group(function (): void {
            Route::get(
                'create/{step?}',
                [CharactersController::class, 'create'],
            )->name('create');
            Route::post(
                'create/background',
                [CharactersController::class, 'storeBackground']
            )->name('create-background');
            Route::post(
                'create/caste',
                [CharactersController::class, 'storeCaste']
            )->name('create-caste');
            Route::post(
                'create/hooks',
                [CharactersController::class, 'storeHooks']
            )->name('create-hooks');
            Route::post(
                'create/ideology',
                [CharactersController::class, 'storeIdeology']
            )->name('create-ideology');
            Route::post(
                'create/impulse',
                [CharactersController::class, 'storeImpulse']
            )->name('create-impulse');
            Route::post(
                'create/lineage',
                [CharactersController::class, 'storeLineage']
            )->name('create-lineage');
            Route::post(
                'create/origin',
                [CharactersController::class, 'storeOrigin']
            )->name('create-origin');
            Route::post(
                'create/relations',
                [CharactersController::class, 'storeRelations']
            )->name('create-relations');
            Route::post(
                'create/values',
                [CharactersController::class, 'storeValues']
            )->name('create-values');
        });
});

Route::get(
    '/characters/subversion/{character}',
    [CharactersController::class, 'view']
)->name('subversion.character');
