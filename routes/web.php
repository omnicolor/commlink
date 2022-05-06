<?php

declare(strict_types=1);

use App\Http\Controllers\CampaignsController;
use App\Http\Controllers\Capers\CharactersController as CapersCharacterController;
use App\Http\Controllers\CyberpunkRed\CharactersController as CyberpunkRedCharacterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiscordController;
use App\Http\Controllers\Expanse\CharactersController as ExpanseCharacterController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Shadowrun5E\CharactersController as Shadowrun5ECharacterController;
use Illuminate\Support\Facades\Route;

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
        '/campaigns/{campaign}/gm-screen',
        [CampaignsController::class, 'gmScreen']
    )->name('campaign.gm-screen');

    Route::get('/discord', [DiscordController::class, 'view'])
        ->name('discord.view');
    Route::post('/discord', [DiscordController::class, 'save'])
        ->name('discord.save');

    Route::prefix('characters')->group(function (): void {
        Route::prefix('capers')->name('capers.')->group(function (): void {
            Route::get(
                'create/{step?}',
                [CapersCharacterController::class, 'create']
            )->name('create');

            Route::post(
                'create/anchors',
                [CapersCharacterController::class, 'storeAnchors']
            )->name('create-anchors');
            Route::post(
                'create/basics',
                [CapersCharacterController::class, 'storeBasics']
            )->name('create-basics');
            Route::post(
                'create/boosts',
                [CapersCharacterController::class, 'storeBoosts']
            )->name('create-boosts');
            Route::post(
                'create/gear',
                [CapersCharacterController::class, 'storeGear']
            )->name('create-gear');
            Route::post(
                'create/powers',
                [CapersCharacterController::class, 'storePowers']
            )->name('create-powers');
            Route::post(
                'create/save',
                [CapersCharacterController::class, 'saveCharacter']
            )->name('create-save');
            Route::post(
                'create/skills',
                [CapersCharacterController::class, 'storeSkills']
            )->name('create-skills');
            Route::post(
                'create/traits',
                [CapersCharacterController::class, 'storeTraits']
            )->name('create-traits');
        });

        // TODO: Change name to "cyberpunkred."
        Route::prefix('cyberpunkred')->name('cyberpunkred-')->group(function (): void {
            Route::get(
                'create/{step?}',
                [CyberpunkRedCharacterController::class, 'createForm'],
            );
            Route::post(
                'create/role',
                [CyberpunkRedCharacterController::class, 'storeRole'],
            )->name('create-role');
            Route::post(
                'create/handle',
                [CyberpunkRedCharacterController::class, 'storeHandle'],
            )->name('create-handle');
            Route::post(
                'create/lifepath',
                [CyberpunkRedCharacterController::class, 'storeLifepath'],
            )->name('create-lifepath');
            Route::post(
                'create/stats',
                [CyberpunkRedCharacterController::class, 'storeStats'],
            )->name('create-stats');
        });

        Route::prefix('shadowrun5e')->name('shadowrun5e.')->group(function (): void {
            Route::get('/', [Shadowrun5ECharacterController::class, 'list']);
            Route::get(
                'create/{step?}',
                [Shadowrun5ECharacterController::class, 'create'],
            );
            Route::post(
                'create/attributes',
                [Shadowrun5ECharacterController::class, 'storeAttributes'],
            )->name('create-attributes');
            Route::post(
                'create/martial-arts',
                [Shadowrun5ECharacterController::class, 'storeMartialArts'],
            )->name('create-martial-arts');
            Route::post(
                'create/qualities',
                [Shadowrun5ECharacterController::class, 'storeQualities'],
            )->name('create-qualities');
            Route::post(
                'create/rules',
                [Shadowrun5ECharacterController::class, 'storeRules'],
            )->name('create-rules');
            Route::post(
                'create/skills',
                [Shadowrun5ECharacterController::class, 'storeSkills'],
            )->name('create-skills');
            Route::post(
                'create/standard',
                [Shadowrun5ECharacterController::class, 'storeStandard'],
            )->name('create-standard');
            Route::post(
                'create/vitals',
                [Shadowrun5ECharacterController::class, 'storeVitals'],
            )->name('create-vitals');
        });
    });

    Route::get('/settings', [SettingsController::class, 'show'])
        ->name('settings')->middleware('web');
    Route::post('/settings/link-user', [SettingsController::class, 'linkUser'])
        ->name('settings-link-user');
});

Route::get('/', function () {
    return view('welcome');
});
Route::get('/about', function () {
    return view('about');
});

// Allow character sheets to be viewed without being logged in.
Route::get(
    '/characters/capers/{character}',
    [CapersCharacterController::class, 'view']
)->name('capers.character');
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
