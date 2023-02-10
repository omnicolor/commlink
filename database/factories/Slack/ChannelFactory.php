<?php

declare(strict_types=1);

namespace Database\Factories\Slack;

use App\Models\Channel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Channel>
 */
class ChannelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     * @var string
     */
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
            'system' => $this->faker->randomElement(config('app.systems')),
        ];
    }
}
