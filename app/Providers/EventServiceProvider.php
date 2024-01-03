<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\DiscordMessageReceived;
use App\Events\EventCreated;
use App\Events\InitiativeAdded;
use App\Events\RollEvent;
use App\Events\Shadowrun5e\DamageEvent;
use App\Listeners\HandleDiscordMessage;
use App\Listeners\HandleEventCreated;
use App\Listeners\HandleInitiativeEvent;
use App\Listeners\HandleRollEvent;
use App\Listeners\Shadowrun5e\HandleDamageEvent;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use SocialiteProviders\Discord\DiscordExtendSocialite;
use SocialiteProviders\Google\GoogleExtendSocialite;
use SocialiteProviders\Manager\SocialiteWasCalled;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        DamageEvent::class => [
            HandleDamageEvent::class,
        ],
        DiscordMessageReceived::class => [
            HandleDiscordMessage::class,
        ],
        EventCreated::class => [
            HandleEventCreated::class,
        ],
        InitiativeAdded::class => [
            HandleInitiativeEvent::class,
        ],
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        RollEvent::class => [
            HandleRollEvent::class,
        ],
        SocialiteWasCalled::class => [
            DiscordExtendSocialite::class . '@handle',
            GoogleExtendSocialite::class . '@handle',
        ],
    ];

    /**
     * Subscriber classes to listen for.
     * @var array<array-key, mixed>
     */
    protected $subscribe = [];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
    }
}
