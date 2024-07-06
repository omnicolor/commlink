<?php

declare(strict_types=1);

use App\Http\Controllers\CampaignsController;
use App\Http\Controllers\ChannelsController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\Fakes\NamesController as FakeNamesController;
use App\Http\Controllers\HealthzController;
use App\Http\Controllers\InitiativesController;
use App\Http\Controllers\SlackController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VarzController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::resource('/channels', ChannelsController::class)
        ->only(['update']);
    Route::resource('/campaigns', CampaignsController::class)
        ->only(['destroy', 'index', 'show']);
    Route::patch('/campaigns/{campaign}', [CampaignsController::class, 'patch'])
        ->name('campaign.patch');
    Route::resource(
        '/campaigns/{campaign}/initiatives',
        InitiativesController::class,
    );
    Route::delete(
        '/campaigns/{campaign}/initiatives',
        [InitiativesController::class, 'truncate'],
    );
    Route::get(
        '/campaigns/{campaign}/events',
        [EventsController::class, 'indexForCampaign'],
    )->name('events.campaign-index');
    Route::post(
        '/campaigns/{campaign}/events',
        [EventsController::class, 'store'],
    )->name('events.store');
    Route::post(
        '/campaigns/{campaign}/invite',
        [CampaignsController::class, 'invite'],
    )->name('campaign.invite');

    Route::resource('events', EventsController::class)
        ->withTrashed(['destroy'])
        ->except(['edit', 'store', 'update']);
    Route::patch('events/{event}', [EventsController::class, 'patch'])
        ->name('events.patch');
    Route::put('events/{event}', [EventsController::class, 'put'])
        ->name('events.put');
    Route::get('events/{event}/rsvp', [EventsController::class, 'getRsvp'])
        ->name('events.rsvp.show');
    Route::delete('events/{event}/rsvp', [EventsController::class, 'deleteRsvp'])
        ->name('events.delete-rsvp');
    Route::put('events/{event}/rsvp', [EventsController::class, 'updateRsvp'])
        ->name('events.update-rsvp');

    Route::prefix('fakes')->name('fakes.')->group(function (): void {
        Route::get('names', FakeNamesController::class)->name('names');
    });

    Route::resource('users', UsersController::class)
        ->only(['index', 'show', 'update']);
    Route::post('users/{user}/token', [UsersController::class, 'createToken'])
        ->name('create-token');
    Route::delete(
        'users/{user}/token/{tokenId}',
        [UsersController::class, 'deleteToken'],
    )->name('delete-token');
});

Route::options('/roll', [SlackController::class, 'options'])
    ->name('roll-options');
Route::post('/roll', [SlackController::class, 'post'])->name('roll');
Route::get('/healthz', HealthzController::class)->name('healthz');
Route::get('/varz', [VarzController::class, 'index'])->name('varz');
