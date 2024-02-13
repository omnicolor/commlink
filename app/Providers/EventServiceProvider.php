<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\CampaignCreated;
use App\Events\CampaignInvitationCreated;
use App\Events\CampaignInvitationUpdated;
use App\Events\ChannelLinked;
use App\Events\DiscordMessageReceived;
use App\Events\DiscordUserLinked;
use App\Events\EventCreated;
use App\Events\InitiativeAdded;
use App\Events\IrcMessageReceived;
use App\Events\RollEvent;
use App\Events\Shadowrun5e\DamageEvent as Shadowrun5eDamage;
use App\Events\SlackUserLinked;
use App\Listeners\HandleDiscordMessage;
use App\Listeners\HandleEventCreated;
use App\Listeners\HandleInitiativeEvent;
use App\Listeners\HandleIrcMessage;
use App\Listeners\HandleRollEvent;
use App\Listeners\SendEmailOnCampaignInvitationCreated;
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
        CampaignCreated::class => [],
        CampaignInvitationCreated::class => [
            SendEmailOnCampaignInvitationCreated::class,
        ],
        CampaignInvitationUpdated::class => [],
        ChannelLinked::class => [],
        DiscordMessageReceived::class => [
            HandleDiscordMessage::class,
        ],
        DiscordUserLinked::class => [],
        EventCreated::class => [
            HandleEventCreated::class,
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
        Shadowrun5eDamage::class => [
            HandleDamageEvent::class,
        ],
        SocialiteWasCalled::class => [
            DiscordExtendSocialite::class . '@handle',
            GoogleExtendSocialite::class . '@handle',
        ],
        SlackUserLinked::class => [],
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
