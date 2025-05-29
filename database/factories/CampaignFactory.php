<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Campaign;

use function array_keys;

/**
 * @extends Factory<Campaign>
 * @method mixed hasChannels(int $count, array{type: string, webhook?: string} $channel)
 * @method mixed hasInitiatives(int $count)
 */
class CampaignFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'description' => $this->faker->text(),
            'registered_by' => $this->createUser(),
            'gm' => $this->createUser(),
            'system' => $this->faker->randomElement(
                array_keys(config('commlink.systems'))
            ),
            'options' => [
                'gameplay' => 'established',
                'startDate' => '2080-01-01',
            ],
        ];
    }
}
