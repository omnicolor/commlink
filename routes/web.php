<?php

declare(strict_types=1);

use App\Http\Controllers\CampaignsController;
use App\Http\Controllers\CyberpunkRed\CharactersController as CyberpunkRedCharacterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Expanse\CharactersController as ExpanseCharacterController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Shadowrun5E\CharactersController as Shadowrun5ECharacterController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'show'])
        ->name('dashboard');

    Route::get('/campaigns/create', [CampaignsController::class, 'createForm'])
        ->name('campaign.createForm');
    Route::post('/campaigns/create', [CampaignsController::class, 'create'])
        ->name('campaign.create');
    Route::get('/campaigns/{campaign}', [CampaignsController::class, 'view'])
        ->name('campaign.view');

    Route::get(
        '/characters/cyberpunkred/create/{step?}',
        [CyberpunkRedCharacterController::class, 'createForm'],
    );
    Route::post(
        '/characters/cyberpunkred/create/role',
        [CyberpunkRedCharacterController::class, 'storeRole'],
    )->name('cyberpunkred-create-role');
    Route::post(
        '/characters/cyberpunkred/create/handle',
        [CyberpunkRedCharacterController::class, 'storeHandle'],
    )->name('cyberpunkred-create-handle');
    Route::post(
        '/characters/cyberpunkred/create/lifepath',
        [CyberpunkRedCharacterController::class, 'storeLifepath'],
    )->name('cyberpunkred-create-lifepath');
    Route::post(
        '/characters/cyberpunkred/create/stats',
        [CyberpunkRedCharacterController::class, 'storeStats'],
    )->name('cyberpunkred-create-stats');

    Route::get(
        '/characters/shadowrun5e',
        [Shadowrun5ECharacterController::class, 'list']
    );

    Route::get('/settings', [SettingsController::class, 'show'])
        ->name('settings')->middleware('web');
    Route::post('/settings/link-user', [SettingsController::class, 'linkUser'])
        ->name('settings-link-user');
});

Route::get(
    '/characters/cyberpunkred/{character}',
    [CyberpunkRedCharacterController::class, 'view']
)->name('cyberpunk.character');

Route::get(
    '/characters/expanse/{character}',
    [ExpanseCharacterController::class, 'view']
)->name('expanse.character');

Route::get(
    '/characters/shadowrun5e/{character}',
    [Shadowrun5ECharacterController::class, 'view']
)->name('shadowrun5e.character');

require __DIR__ . '/auth.php';
