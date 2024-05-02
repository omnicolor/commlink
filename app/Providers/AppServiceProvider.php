<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\ServiceProvider;
use Laravel\Pennant\Feature;

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
            $dbName = env('MONGO_DATABASE') . $token;
            config(['database.connections.mongodb.database' => $dbName]);
        });
    }
}
