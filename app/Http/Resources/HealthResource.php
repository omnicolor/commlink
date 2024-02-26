<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @codeCoverageIgnore
 */
class HealthResource extends JsonResource
{
    /**
     * @var array<string, string>
     */
    protected array $statuses;

    public function __construct(mixed $resource, protected int $time)
    {
        parent::__construct($resource);
        $this->statuses = (array)$resource;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => [
                'data' => $this->statuses['data'],
                'database' => [
                    'document' => $this->statuses['mongo'],
                    'key_value' => $this->when(
                        config('health.redis'),
                        $this->statuses['redis'] ?? null,
                    ),
                    'relational' => $this->statuses['mysql'],
                ],
                'disk_space' => $this->statuses['disk'],
                'workers' => [
                    'discord' => $this->when(
                        config('health.discord'),
                        $this->statuses['discord'] ?? null,
                    ),
                    'irc' => $this->when(
                        config('health.irc'),
                        $this->statuses['irc'] ?? null,
                    ),
                    'queue' => $this->statuses['queue'],
                    'schedule' => $this->statuses['schedule'],
                ],
                'links' => [
                    'self' => route('healthz'),
                    'statistics' => route('varz'),
                ],
            ],
            'meta' => [
                // hrtime measures time in nano seconds, convert to seconds.
                'time_in_seconds' => $this->time / 1_000_000_000,
            ],
        ];
    }
}
