<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Pennant\Feature;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;
use Laravel\Telescope\TelescopeServiceProvider as Telescope;

use function abort;
use function config;
use function env;
use function optional;
use function touch;

/**
 * @codeCoverageIgnore
 * @psalm-suppress UnusedClass
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/dashboard';

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
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function boot(): void
    {
        Feature::discover();
        ParallelTesting::setUpTestCase(function (int $token): void {
            $mongo = env('MONGO_DATABASE') . $token;
            config(['database.connections.mongodb.database' => $mongo]);

            touch((string)env('DB_DATABASE'));
            $sqlite = env('DB_DATABASE') . $token;
            touch($sqlite);
        });
        Gate::define('viewPulse', function (User $user): bool {
            return $user->hasRole('admin');
        });

        $this->bootRoute();
    }

    public function bootRoute(): void
    {
        RateLimiter::for('api', function (Request $request): Limit {
            return Limit::perMinute(60)->by(optional($request->user())->id ?? $request->ip());
        });

        EnsureFeaturesAreActive::whenInactive(
            function (): void {
                abort(Response::HTTP_NOT_FOUND);
            }
        );
    }
}
