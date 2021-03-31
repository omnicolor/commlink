<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\DiscordMessageReceived;
use App\Listeners\HandleDiscordListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     * @var array<string, string[]>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        DiscordMessageReceived::class => [
            HandleDiscordListener::class,
        ],
    ];

    /**
     * Subscriber classes to listen for.
     * @var array<int, string>
     */
    protected $subscribe = [];
}
