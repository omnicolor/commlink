<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\ServiceProvider;
use Laravel\Pennant\Feature;

/**
 * @psalm-suppress UnusedClass
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Feature::discover();
        ParallelTesting::setUpTestCase(function (int $token): void {
            $mongo = env('MONGO_DATABASE') . $token;
            config(['database.connections.mongodb.database' => $mongo]);

            touch(env('DB_DATABASE'));
            $sqlite = env('DB_DATABASE') . $token;
            touch($sqlite);
        });
        Gate::define('viewPulse', function (User $user): bool {
            return $user->hasRole('admin');
        });
    }
}
