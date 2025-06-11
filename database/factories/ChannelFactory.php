<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ChannelType;
use App\Models\Channel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Override;

use function array_keys;
use function config;

/**
 * @extends Factory<Channel>
 * @method mixed hasChannels(int $count, array $parameters)
 * @method mixed hasInitiatives(int $count)
 */
class ChannelFactory extends Factory
{
    /**
     * @return array{
     *     campaign_id: null,
     *     channel_id: string,
     *     channel_name: string,
     *     registered_by: int,
     *     server_id: string,
     *     server_name: string,
     *     system: string,
     *     type: string
     * }
     */
    #[Override]
    public function definition(): array
    {
        return [
            'campaign_id' => null,
            'channel_id' => Str::random(10),
            'channel_name' => $this->faker->company(),
            'registered_by' => User::factory()->create()->id,
            'server_id' => Str::random(10),
            'server_name' => $this->faker->company(),
            'system' => (string)$this->faker->randomElement(
                array_keys((array)config('commlink.systems'))
            ),
            'type' => ($this->faker->randomElement(ChannelType::cases()))->value,
        ];
    }
}
