<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Channel;
use Illuminate\Support\Str;

use function array_keys;

/**
 * @extends Factory<Channel>
 * @method mixed hasChannels(int $count, array $parameters)
 * @method mixed hasInitiatives(int $count)
 * @psalm-suppress UnusedClass
 */
class ChannelFactory extends Factory
{
    /**
     * @var class-string
     */
    protected $model = Channel::class;

    /**
     * Define the model's default state.
     * @return array<string, int|null|string>
     */
    public function definition(): array
    {
        return [
            'campaign_id' => null,
            'channel_id' => Str::random(10),
            'channel_name' => $this->faker->company(),
            'registered_by' => $this->createUser()->id,
            'server_id' => Str::random(10),
            'server_name' => $this->faker->company(),
            'system' => (string)$this->faker->randomElement(
                array_keys((array)config('app.systems'))
            ),
            'type' => (string)$this->faker->randomElement(Channel::VALID_TYPES),
        ];
    }
}
