<?php

declare(strict_types=1);

use App\Http\Controllers\DiceRollerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::options('/roll', [DiceRollerController::class, 'options'])
    ->name('roll-options');
Route::post('/roll', [DiceRollerController::class, 'post'])->name('roll');

require __DIR__ . '/shadowrun5e.php';
