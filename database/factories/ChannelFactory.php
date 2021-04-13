<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Channel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChannelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Channel::class;

    /**
     * Define the model's default state.
     * @return array
     */
    public function definition(): array
    {
        return [
            'channel_id' => \Str::random(10),
            'channel_name' => $this->faker->company(),
            'registered_by' => User::factory()->create(),
            'server_id' => \Str::random(10),
            'server_name' => $this->faker->company(),
            'system' => $this->faker->randomElement(
                array_keys(config('app.systems'))
            ),
            'type' => $this->faker->randomElement(Channel::VALID_TYPES),
        ];
    }
}
