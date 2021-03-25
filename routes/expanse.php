<?php

declare(strict_types=1);

use App\Http\Controllers\Expanse\SocialClassesController;
use App\Http\Controllers\Expanse\TalentsController;

Route::middleware('auth:sanctum')->prefix('expanse')->name('expanse.')->group(
    function (): void {
        Route::resource('social-classes', SocialClassesController::class)
            ->only(['index', 'show']);
        Route::resource('talents', TalentsController::class)
            ->only(['index', 'show']);
    }
);
