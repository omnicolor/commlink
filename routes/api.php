<?php

declare(strict_types=1);

use App\Http\Controllers\ChannelsController;
use App\Http\Controllers\InitiativesController;
use App\Http\Controllers\SlackController;
use App\Http\Controllers\VarzController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::resource('/channels', ChannelsController::class)
        ->only(['update']);
    Route::resource(
        '/campaigns/{campaign}/initiatives',
        InitiativesController::class,
    );
    Route::delete(
        '/campaigns/{campaign}/initiatives',
        [InitiativesController::class, 'truncate']
    );
});

Route::options('/roll', [SlackController::class, 'options'])
    ->name('roll-options');
Route::post('/roll', [SlackController::class, 'post'])->name('roll');
Route::get('/varz', [VarzController::class, 'index']);

require __DIR__ . '/cyberpunkred.php';
require __DIR__ . '/dnd5e.php';
require __DIR__ . '/expanse.php';
require __DIR__ . '/shadowrun5e.php';
