<?php

declare(strict_types=1);

namespace App\Providers;

use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;
use Override;

/**
 * @codeCoverageIgnore
 */
class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     */
    #[Override]
    public function register(): void
    {
        // Telescope::night();

        $this->hideSensitiveRequestDetails();

        $isLocal = (bool)$this->app->environment('local');

        Telescope::filter(function (IncomingEntry $entry) use ($isLocal): bool {
            if ($isLocal) {
                return true;
            }
            if ($entry->isReportableException()) {
                return true;
            }
            if ($entry->isFailedRequest()) {
                return true;
            }
            if ($entry->isFailedJob()) {
                return true;
            }
            if ($entry->isScheduledTask()) {
                return true;
            }
            return $entry->hasMonitoredTag();
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if (true === $this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     */
    #[Override]
    protected function gate(): void
    {
    }
}
