<?php

declare(strict_types=1);

use App\Http\Controllers\ChannelsController;
use App\Http\Controllers\SlackController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function (): void {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

Route::middleware('auth:sanctum')->group(function (): void {
    Route::resource('/channels', ChannelsController::class)
        ->only(['update']);
});

Route::options('/roll', [SlackController::class, 'options'])
    ->name('roll-options');
Route::post('/roll', [SlackController::class, 'post'])->name('roll');

require __DIR__ . '/cyberpunkred.php';
require __DIR__ . '/dnd5e.php';
require __DIR__ . '/expanse.php';
require __DIR__ . '/shadowrun5e.php';
