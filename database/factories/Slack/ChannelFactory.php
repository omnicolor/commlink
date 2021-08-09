<?php

declare(strict_types=1);

namespace Database\Factories\Slack;

use App\Models\Slack\Channel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

class ChannelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = Channel::class;

    /**
     * Define the model's default state.
     * @return array<int, string>
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
