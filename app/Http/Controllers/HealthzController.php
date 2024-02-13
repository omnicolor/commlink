<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

use function count;
use function disk_free_space;
use function disk_total_space;
use function escapeshellarg;
use function explode;
use function shell_exec;

use const PHP_EOL;

/**
 * @codeCoverageIgnore
 */
class HealthzController extends Controller
{
    protected function checkData(): bool
    {
        return Command::SUCCESS === Artisan::call('commlink:validate-data-files');
    }

    protected function checkDiscord(): bool
    {
        return 0 !== count($this->lookForProcess('commlink:discord-run'));
    }

    protected function checkDiskSpace(): bool
    {
        $percent = (float)disk_free_space(__DIR__)
            / (float)disk_total_space(__DIR__)
            * 100;
        return 80 >= $percent;
    }

    protected function checkMongo(): bool
    {
        return 0 !== DB::connection('mongodb')->table('characters')->count();
    }

    protected function checkMySQL(): bool
    {
        try {
            return 0 !== DB::connection('mysql')->table('users')->count();
        } catch (QueryException) {
            return false;
        }
    }

    protected function checkRedis(): bool
    {
        $now = now()->toDateTimeString();
        Redis::set('healthz', $now);
        return Redis::get('healthz') === $now;
    }

    protected function checkQueue(): bool
    {
        return 0 !== count($this->lookForProcess('queue:work'))
            || 0 !== count($this->lookForProcess('queue:listen'));
    }

    protected function checkSchedule(): bool
    {
        return 0 !== count($this->lookForProcess('schedule:work'));
    }

    /**
     * @return array<int, string>
     */
    protected function lookForProcess(string $name): array
    {
        $command = sprintf(
            'ps ax | grep %s | grep -v "ps ax" | grep -v grep',
            escapeshellarg($name),
        );
        $output = (string)shell_exec($command);
        return explode(PHP_EOL, $output);
    }

    public function __invoke(): JsonResponse
    {
        $start = hrtime(true);
        $statuses = [
            'data' => $this->checkData(),
            'discord' => $this->checkDiscord(),
            'disk' => $this->checkDiskSpace(),
            'mongo' => $this->checkMongo(),
            'mysql' => $this->checkMySQL(),
            'queue' => $this->checkQueue(),
            'redis' => $this->checkRedis(),
            'schedule' => $this->checkSchedule(),
        ];

        if (in_array(false, $statuses, true)) {
            $status = JsonResponse::HTTP_SERVICE_UNAVAILABLE;
        } else {
            $status = JsonResponse::HTTP_OK;
        }

        array_walk($statuses, function (bool &$status): void {
            if (false === $status) {
                $status = 'failed';
            } else {
                $status = 'OK';
            }
        });
        return new JsonResponse(
            [
                'data' => [
                    'data' => $statuses['data'],
                    'database' => [
                        'document' => $statuses['mongo'],
                        'key_value' => $statuses['redis'],
                        'relational' => $statuses['mysql'],
                    ],
                    'disk_space' => $statuses['disk'],
                    'links' => [
                        'self' => route('healthz'),
                        'statistics' => route('varz'),
                    ],
                    'workers' => [
                        'discord' => $statuses['discord'],
                        'queue' => $statuses['queue'],
                        'schedule' => $statuses['schedule'],
                    ],
                ],
                'meta' => [
                    // hrtime measures time in nano seconds, convert to seconds.
                    'time_in_seconds' => (hrtime(true) - $start) / 1_000_000_000,
                ],
            ],
            $status,
        );
    }
}
