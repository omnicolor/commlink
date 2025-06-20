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
     * @return array{
     *     campaign_id: null,
     *     channel_id: null,
     *     character_id: null,
     *     character_name: string,
     *     initiative: int
     * }
     */
    #[Override]
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
