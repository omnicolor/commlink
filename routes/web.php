<?php

declare(strict_types=1);

use App\Features\Stillfleet as StillfleetFeature;
use App\Http\Controllers\CampaignsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiscordController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\Import\Chummer5Controller;
use App\Http\Controllers\Import\HeroLabController;
use App\Http\Controllers\Import\WorldAnvilController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SlackController;
use App\Http\Controllers\Stillfleet\CharactersController as StillfleetController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

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
        Route::prefix('stillfleet')
            ->name('stillfleet.')
            ->middleware(EnsureFeaturesAreActive::using(StillfleetFeature::class))
            ->group(function (): void {
                Route::get('', [StillfleetController::class, 'list'])
                    ->name('list');
                Route::get(
                    'create/{step?}',
                    [StillfleetController::class, 'create'],
                )->name('create');
                Route::post(
                    'create/class',
                    [StillfleetController::class, 'saveClass'],
                )->name('create-class');
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

        Route::get('world-anvil', [WorldAnvilController::class, 'view'])
            ->name('world-anvil.view');
        Route::post('world-anvil', [WorldAnvilController::class, 'upload'])
            ->name('world-anvil.upload');
    });

    Route::get('/settings', [SettingsController::class, 'show'])
        ->name('settings')->middleware('web');
    Route::post('/settings/link-user', [SettingsController::class, 'linkUser'])
        ->name('settings-link-user');
});

Route::group(['middleware' => ['auth', 'permission:admin users']], function (): void {
    Route::get('users', [UsersController::class, 'view'])->name('users.view');
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
Route::get(
    '/characters/stillfleet/{character}',
    [StillfleetController::class, 'view']
)
    ->name('stillfleet.character')
    ->middleware(EnsureFeaturesAreActive::using(StillfleetFeature::class));

require __DIR__ . '/auth.php';
