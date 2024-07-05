<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Expanse\Http\Controllers\BackgroundsController;
use Modules\Expanse\Http\Controllers\CharactersController;
use Modules\Expanse\Http\Controllers\ConditionsController;
use Modules\Expanse\Http\Controllers\FocusesController;
use Modules\Expanse\Http\Controllers\SocialClassesController;
use Modules\Expanse\Http\Controllers\TalentsController;

Route::middleware('auth:sanctum')
    ->prefix('expanse')
    ->name('expanse.')
    ->group(function (): void {
        Route::resource('backgrounds', BackgroundsController::class)
            ->only(['index', 'show']);
        Route::resource('characters', CharactersController::class)
            ->only(['index', 'show']);
        Route::resource('conditions', ConditionsController::class)
            ->only(['index', 'show']);
        Route::resource('focuses', FocusesController::class)
            ->only(['index', 'show']);
        Route::resource('social-classes', SocialClassesController::class)
            ->only(['index', 'show']);
        Route::resource('talents', TalentsController::class)
            ->only(['index', 'show']);
    });
