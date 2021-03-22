<?php

declare(strict_types=1);

use App\Http\Controllers\CyberpunkRed\CharactersController as CyberpunkRedCharacterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Shadowrun5E\CharactersController as Shadowrun5ECharacterController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'show'])
        ->name('dashboard');
    Route::get('/settings', [SettingsController::class, 'show'])
        ->name('settings')->middleware('web');
    Route::post(
        '/settings/link-slack',
        [SettingsController::class, 'linkSlack']
    );
});

Route::get(
    '/characters/shadowrun5e/{character}',
    [Shadowrun5ECharacterController::class, 'view']
)->name('shadowrun5e.character');

Route::get(
    '/characters/cyberpunkred/{character}',
    [CyberpunkRedCharacterController::class, 'view']
)->name('shadowrun5e.character');

require __DIR__ . '/auth.php';
