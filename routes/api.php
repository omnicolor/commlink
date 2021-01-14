<?php

declare(strict_types=1);

use App\Http\Controllers\Shadowrun5E\AdeptPowersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('shadowrun5e')->name('shadowrun5e.')->group(function () {
    Route::resource('adept-powers', AdeptPowersController::class)
        ->only(['index', 'show']);
});
