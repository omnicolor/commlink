<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Campaign;

/**
 * @extends Factory<Campaign>
 * @method mixed hasChannels(int $count, array<string, ?string> $channel)
 * @method mixed hasInitiatives(int $count)
 * @psalm-suppress UnusedClass
 */
class CampaignFactory extends Factory
{
    protected $model = Campaign::class;

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
                \array_keys(config('app.systems'))
            ),
            'options' => [
                'gameplay' => 'established',
                'startDate' => '2080-01-01',
            ],
        ];
    }
}
