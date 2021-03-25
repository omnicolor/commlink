<?php

declare(strict_types=1);

use App\Http\Controllers\Expanse\BackgroundsController;
use App\Http\Controllers\Expanse\ConditionsController;
use App\Http\Controllers\Expanse\FocusesController;
use App\Http\Controllers\Expanse\SocialClassesController;
use App\Http\Controllers\Expanse\TalentsController;

Route::middleware('auth:sanctum')->prefix('expanse')->name('expanse.')->group(
    function (): void {
        Route::resource('backgrounds', BackgroundsController::class)
            ->only(['index', 'show']);
        Route::resource('conditions', ConditionsController::class)
            ->only(['index', 'show']);
        Route::resource('focuses', FocusesController::class)
            ->only(['index', 'show']);
        Route::resource('social-classes', SocialClassesController::class)
            ->only(['index', 'show']);
        Route::resource('talents', TalentsController::class)
            ->only(['index', 'show']);
    }
);
