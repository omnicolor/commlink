<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Override;

use function array_keys;
use function config;

/**
 * @extends Factory<Campaign>
 * @method mixed hasChannels(int $count, array{type: string, webhook?: string} $channel)
 * @method mixed hasInitiatives(int $count)
 */
class CampaignFactory extends Factory
{
    /**
     * @return array{
     *     name: string,
     *     description: string,
     *     registered_by: int,
     *     gm: int,
     *     system: string,
     *     options: array{
     *         gameplay: string,
     *         startDate: string
     *     }
     * }
     */
    #[Override]
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'description' => $this->faker->text(),
            'registered_by' => User::factory()->create(),
            'gm' => User::factory()->create(),
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
