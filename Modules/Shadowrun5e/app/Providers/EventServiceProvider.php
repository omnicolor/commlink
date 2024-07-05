<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Shadowrun5e\Events\DamageEvent;
use Modules\Shadowrun5e\Listeners\HandleDamageEvent;

/**
 * @psalm-suppress UnusedClass
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        DamageEvent::class => [
            HandleDamageEvent::class,
        ],
    ];

    /**
     * Indicates if events should be discovered.
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void
    {
    }
}
