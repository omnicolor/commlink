<?php

declare(strict_types=1);

namespace Database\Factories\Slack;

use App\Models\Channel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Channel>
 * @psalm-suppress UnusedClass
 */
class ChannelFactory extends Factory
{
    protected $model = Channel::class;

    /**
     * Define the model's default state.
     * @return array<string, string>
     */
    public function definition(): array
    {
        return [
            'channel' => Str::random(10),
            'team' => 'T' . Str::random(9),
            'system' => (string)$this->faker->randomElement(config('app.systems')),
        ];
    }
}
