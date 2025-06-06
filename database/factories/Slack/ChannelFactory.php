<?php

declare(strict_types=1);

namespace Database\Factories\Slack;

use App\Models\Channel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

use function config;

/**
 * @extends Factory<Channel>
 */
class ChannelFactory extends Factory
{
    protected $model = Channel::class;

    /**
     * @return array{
     *     channel: string,
     *     team: string,
     *     system: string
     * }
     */
    public function definition(): array
    {
        return [
            'channel' => Str::random(10),
            'team' => 'T' . Str::random(9),
            'system' => (string)$this->faker->randomElement(config('commlink.systems')),
        ];
    }
}
