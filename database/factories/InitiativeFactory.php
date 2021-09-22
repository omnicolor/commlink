<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\Initiative;
use Illuminate\Database\Eloquent\Factories\Factory;

class InitiativeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = Initiative::class;

    /**
     * Define the model's default state.
     * @return array<string, string>
     */
    public function definition(): array
    {
        return [
            'campaign_id' => Campaign::factory()->create(),
            'channel_id' => null,
            'character_id' => null,
            'character_name' => $this->faker->name,
            'initiative' => $this->faker->randomDigit(),
        ];
    }
}
