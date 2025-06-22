<?php

declare(strict_types=1);

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Override;

use function base_path;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     * @codeCoverageIgnore
     */
    #[Override]
    protected function schedule(Schedule $schedule): void
    {
    }

    /**
     * Register the commands for the application.
     */
    #[Override]
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
