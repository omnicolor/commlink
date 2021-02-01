<?php

declare(strict_types=1);

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Shadowrun5E\CharactersController as Shadowrun5ECharacterController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'show'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::get(
    '/characters/shadowrun5e/{character}',
    [Shadowrun5ECharacterController::class, 'view']
)->name('shadowrun5e.character');

require __DIR__ . '/auth.php';
