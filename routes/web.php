<?php

declare(strict_types=1);

use App\Http\Controllers\Avatar\CharactersController as AvatarController;
use App\Http\Controllers\CampaignsController;
use App\Http\Controllers\Capers\CharactersController as CapersCharacterController;
use App\Http\Controllers\CyberpunkRed\CharactersController as CyberpunkRedCharacterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiscordController;
use App\Http\Controllers\Expanse\CharactersController as ExpanseCharacterController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Shadowrun5e\CharactersController as ShadowrunController;
use App\Http\Controllers\StarTrekAdventures\CharactersController as StarTrekController;
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
            Route::get('/', [ShadowrunController::class, 'list']);
            Route::get(
                'create/{step?}',
                [ShadowrunController::class, 'create'],
            );
            Route::post(
                'create/attributes',
                [ShadowrunController::class, 'storeAttributes'],
            )->name('create-attributes');
            Route::post(
                'create/knowledge',
                [ShadowrunController::class, 'storeKnowledgeSkills'],
            )->name('create-knowledge-skills');
            Route::post(
                'create/martial-arts',
                [ShadowrunController::class, 'storeMartialArts'],
            )->name('create-martial-arts');
            Route::post(
                'create/qualities',
                [ShadowrunController::class, 'storeQualities'],
            )->name('create-qualities');
            Route::post(
                'create/rules',
                [ShadowrunController::class, 'storeRules'],
            )->name('create-rules');
            Route::post(
                'create/skills',
                [ShadowrunController::class, 'storeSkills'],
            )->name('create-skills');
            Route::post(
                'create/standard',
                [ShadowrunController::class, 'storeStandard'],
            )->name('create-standard');
            Route::post(
                'create/vitals',
                [ShadowrunController::class, 'storeVitals'],
            )->name('create-vitals');
        });

        Route::prefix('star-trek-adventures')->name('star-trek-adventures.')->group(function (): void {
            Route::get('/', [StarTrekController::class, 'list']);
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
Route::get('/characters/avatar/{character}', [AvatarController::class, 'view'])
    ->name('avatar.character');
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
    [ShadowrunController::class, 'view']
)->name('shadowrun5e.character');
Route::get(
    '/characters/star-trek-adventures/{character}',
    [StarTrekController::class, 'view']
)->name('star-trek-adventures.character');

require __DIR__ . '/auth.php';
