<?php

declare(strict_types=1);

use App\Features\Stillfleet as StillfleetFeature;
use Illuminate\Support\Facades\Route;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;
use Modules\Stillfleet\Http\Controllers\CharactersController;

Route::prefix('characters/stillfleet')
    ->name('stillfleet.')
    ->middleware('auth')
    ->middleware(EnsureFeaturesAreActive::using(StillfleetFeature::class))
    ->group(function (): void {
        Route::get('', [CharactersController::class, 'list'])
            ->name('list');
        Route::get(
            'create/save-for-later',
            [CharactersController::class, 'saveForLater'],
        )->name('save-for-later');
        Route::get(
            'create/{step?}',
            [CharactersController::class, 'create'],
        )->name('create');
        Route::post(
            'create/class',
            [CharactersController::class, 'saveClass'],
        )->name('create-class');
        Route::post(
            'create/class-powers',
            [CharactersController::class, 'saveClassPowers'],
        )->name('create-class-powers');
        Route::post(
            'create/details',
            [CharactersController::class, 'saveDetails'],
        )->name('create-details');
        Route::post(
            'create/species',
            [CharactersController::class, 'saveSpecies'],
        )->name('create-species');
        Route::post(
            'create/species-powers',
            [CharactersController::class, 'saveSpeciesPowers'],
        )->name('create-species-powers');
        Route::post(
            'create/attributes',
            [CharactersController::class, 'saveAttributes']
        )->name('create-attributes');
    });

// Allow character sheets to be viewed without being logged in.
Route::get(
    '/characters/stillfleet/{character}',
    [CharactersController::class, 'view']
)
    ->name('stillfleet.character')
    ->middleware(EnsureFeaturesAreActive::using(StillfleetFeature::class));
