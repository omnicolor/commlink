<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Initiative;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Initiative>
 */
class InitiativeFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, int|null|string>
     */
    public function definition(): array
    {
        return [
            'campaign_id' => null,
            'channel_id' => null,
            'character_id' => null,
            'character_name' => $this->faker->name,
            'initiative' => $this->faker->randomDigit(),
        ];
    }
}
