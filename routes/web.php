<?php

declare(strict_types=1);

use App\Http\Controllers\Avatar\CharactersController as AvatarController;
use App\Http\Controllers\CampaignsController;
use App\Http\Controllers\Capers\CharactersController as CapersCharacterController;
use App\Http\Controllers\Cyberpunkred\CharactersController as CyberpunkredController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiscordController;
use App\Http\Controllers\Expanse\CharactersController as ExpanseController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\Import\Chummer5Controller;
use App\Http\Controllers\Import\HeroLabController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Shadowrun5e\CharactersController as ShadowrunController;
use App\Http\Controllers\SlackController;
use App\Http\Controllers\StarTrekAdventures\CharactersController as StarTrekController;
use App\Http\Controllers\Subversion\CharactersController as SubversionController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('discord/auth', [DiscordController::class, 'redirectToDiscord']);
Route::get('discord/callback', [DiscordController::class, 'handleCallback']);
Route::get('google/auth', [GoogleController::class, 'redirectToGoogle']);
Route::get('google/callback', [GoogleController::class, 'handleCallback']);
Route::get('slack/auth', [SlackController::class, 'redirectToSlack']);
Route::get('slack/callback', [SlackController::class, 'handleCallback']);

Route::middleware('auth')->group(function (): void {
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

    // Respond is for an existing user to respond within Commlink. See
    // campaign.invitation-accept, campaign.invitiation-decline, or
    // campaign.invitation-spam for clicking through an invitation email.
    Route::post(
        '/campaigns/{campaign}/respond',
        [CampaignsController::class, 'respond'],
    )->name('campaign.respond');

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

        Route::prefix('cyberpunkred')->name('cyberpunkred.')->group(function (): void {
            Route::get(
                'create/{step?}',
                [CyberpunkredController::class, 'createForm'],
            );
            Route::post(
                'create/role',
                [CyberpunkredController::class, 'storeRole'],
            )->name('create-role');
            Route::post(
                'create/handle',
                [CyberpunkredController::class, 'storeHandle'],
            )->name('create-handle');
            Route::post(
                'create/lifepath',
                [CyberpunkredController::class, 'storeLifepath'],
            )->name('create-lifepath');
            Route::post(
                'create/stats',
                [CyberpunkredController::class, 'storeStats'],
            )->name('create-stats');
        });

        Route::prefix('expanse')->name('expanse.')->group(function (): void {
            Route::get('/', [ExpanseController::class, 'list']);
        });

        Route::prefix('shadowrun5e')->name('shadowrun5e.')->group(function (): void {
            Route::get('/', [ShadowrunController::class, 'list']);
            Route::get(
                'create/save-for-later',
                [ShadowrunController::class, 'saveForLater'],
            );
            Route::get(
                'create/{step?}',
                [ShadowrunController::class, 'create'],
            );
            Route::post(
                'create/attributes',
                [ShadowrunController::class, 'storeAttributes'],
            )->name('create-attributes');
            Route::post(
                'create/background',
                [ShadowrunController::class, 'storeBackground'],
            )->name('create-background');
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
                'create/social',
                [ShadowrunController::class, 'storeSocial'],
            )->name('create-social');
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

        Route::prefix('subversion')
            ->name('subversion.')
            ->group(function (): void {
                Route::get(
                    'create/{step?}',
                    [SubversionController::class, 'create'],
                )->name('create');
                Route::post(
                    'create/background',
                    [SubversionController::class, 'storeBackground']
                )->name('create-background');
                Route::post(
                    'create/caste',
                    [SubversionController::class, 'storeCaste']
                )->name('create-caste');
                Route::post(
                    'create/lineage',
                    [SubversionController::class, 'storeLineage']
                )->name('create-lineage');
                Route::post(
                    'create/origin',
                    [SubversionController::class, 'storeOrigin']
                )->name('create-origin');
            });
    });

    Route::get('/dashboard', [DashboardController::class, 'show'])
        ->name('dashboard');

    // Routes for setting up Discord within the app, not for integrating with
    // an actual Discord server, that's done through an Artisan command.
    Route::get('/discord', [DiscordController::class, 'view'])
        ->name('discord.view');
    Route::post('/discord', [DiscordController::class, 'save'])
        ->name('discord.save');

    Route::prefix('import')->name('import.')->group(function (): void {
        Route::get('chummer5', [Chummer5Controller::class, 'view'])
            ->name('chummer5.view');
        Route::post('chummer5', [Chummer5Controller::class, 'upload'])
            ->name('chummer5.upload');
        Route::get('herolab', [HeroLabController::class, 'view'])
            ->name('herolab.view');
        Route::post('herolab', [HeroLabController::class, 'upload'])
            ->name('herolab.upload');
    });

    Route::get('/settings', [SettingsController::class, 'show'])
        ->name('settings')->middleware('web');
    Route::post('/settings/link-user', [SettingsController::class, 'linkUser'])
        ->name('settings-link-user');
});

Route::group(['middleware' => ['auth', 'permission:admin users']], function (): void {
    Route::resource('users', UsersController::class);
});

Route::get('/', function () {
    return view('welcome');
})->name('welcome');
Route::get('/about', function () {
    return view('about');
});

// Routes for new-to-Commlink users to respond to an invitation.
Route::get(
    '/campaigns/{campaign}/accept/{invitation}/{token}',
    [CampaignsController::class, 'respondAccept'],
)->name('campaign.invitation-accept');
Route::get(
    '/campaigns/{campaign}/decline/{invitation}/{token}',
    [CampaignsController::class, 'respondDecline'],
)->name('campaign.invitation-decline');
Route::get(
    '/campaigns/{campaign}/spam/{invitation}/{token}',
    [CampaignsController::class, 'respondSpam'],
)->name('campaign.invitation-spam');
Route::get(
    '/campaigns/{campaign}/change/{invitation}/{token}',
    [CampaignsController::class, 'respondChangeEmail'],
)->name('campaign.invitation-change');

// Allow character sheets to be viewed without being logged in.
Route::get('/characters/avatar/{character}', [AvatarController::class, 'view'])
    ->name('avatar.character');
Route::get(
    '/characters/capers/{character}',
    [CapersCharacterController::class, 'view']
)->name('capers.character');
Route::get(
    '/characters/cyberpunkred/{character}',
    [CyberpunkredController::class, 'view']
)->name('cyberpunkred.character');
Route::get(
    '/characters/expanse/{character}',
    [ExpanseController::class, 'view']
)->name('expanse.character');
Route::get(
    '/characters/shadowrun5e/{character}',
    [ShadowrunController::class, 'view']
)->name('shadowrun5e.character');
Route::get(
    '/characters/star-trek-adventures/{character}',
    [StarTrekController::class, 'view']
)->name('star-trek-adventures.character');
Route::get(
    '/characters/subversion/{character}',
    [SubversionController::class, 'view']
)->name('subversion.character');

require __DIR__ . '/auth.php';
