<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Campaign;

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
            'system' => $this->faker->randomElement(
                \array_keys(config('app.systems'))
            ),
            'options' => '{"start-date":"2080-01-01","gameplay":"established"}',
        ];
    }
}
