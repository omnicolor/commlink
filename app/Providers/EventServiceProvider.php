<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\DiscordMessageReceived;
use App\Events\InitiativeAdded;
use App\Events\IrcMessageReceived;
use App\Events\RollEvent;
use App\Events\Shadowrun5e\DamageEvent;
use App\Listeners\HandleDiscordMessage;
use App\Listeners\HandleInitiativeEvent;
use App\Listeners\HandleIrcMessage;
use App\Listeners\HandleRollEvent;
use App\Listeners\Shadowrun5e\HandleDamageEvent;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use SocialiteProviders\Discord\DiscordExtendSocialite;
use SocialiteProviders\Google\GoogleExtendSocialite;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Slack\SlackExtendSocialite;

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
        InitiativeAdded::class => [
            HandleInitiativeEvent::class,
        ],
        IrcMessageReceived::class => [
            HandleIrcMessage::class,
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
            SlackExtendSocialite::class . '@handle',
        ],
    ];

    /**
     * Subscriber classes to listen for.
     * @var array<int, string>
     */
    protected $subscribe = [];
}
