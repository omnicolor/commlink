<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\ServiceProvider;
use Laravel\Pennant\Feature;
use Laravel\Telescope\TelescopeServiceProvider as Telescope;

use function config;
use function touch;

/**
 * @codeCoverageIgnore
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (true === $this->app->environment('local')) {
            $this->app->register(Telescope::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Feature::discover();
        ParallelTesting::setUpTestCase(function (int $token): void {
            $mongo = config('database.mongodb.database') . $token;
            config(['database.connections.mongodb.database' => $mongo]);

            $sqlite = config('database.sqlite.database') . $token;
            touch($sqlite);
        });
        Gate::define('viewPulse', function (User $user): bool {
            return $user->hasRole('admin');
        });
    }
}
