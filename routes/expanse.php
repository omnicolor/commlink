<?php

declare(strict_types=1);

use App\Http\Controllers\Expanse\SocialClassesController;

Route::middleware('auth:sanctum')->prefix('expanse')->name('expanse.')->group(
    function (): void {
        Route::resource('social-classes', SocialClassesController::class)
            ->only(['index', 'show']);
    }
);
