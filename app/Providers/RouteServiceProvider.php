<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

use function abort;
use function base_path;
use function optional;

/**
 * @codeCoverageIgnore
 */
class RouteServiceProvider extends ServiceProvider
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
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function (): void {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
        EnsureFeaturesAreActive::whenInactive(
            function (): void {
                abort(Response::HTTP_NOT_FOUND);
            }
        );
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request): Limit {
            // @phpstan-ignore if.condNotBoolean
            if (App::environment('testing')) {
                return Limit::perMinute(6000)
                    ->by(optional($request->user())->id ?? $request->ip());
            }
            return Limit::perMinute(60)->by(optional($request->user())->id ?? $request->ip());
        });
    }
}
