<?php

declare(strict_types=1);

namespace Database\Factories\Shadowrun5e;

use App\Models\Shadowrun5e\Campaign;
use Database\Factories\Factory;

/**
 * @method mixed hasInitiatives(int $count)
 */
class CampaignFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     * @var string
     */
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
            'system' => 'shadowrun5e',
            'options' => [
                'gameplay' => 'established',
                'startDate' => '2080-01-01',
            ],
        ];
    }
}
