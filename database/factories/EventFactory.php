<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Override;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * @return array{
     *     campaign_id: int,
     *     created_by: int,
     *     description: string,
     *     game_end: null,
     *     game_start: null,
     *     name: string,
     *     real_end: null,
     *     real_start: string
     * }
     */
    #[Override]
    public function definition(): array
    {
        return [
            'campaign_id' => (int)Campaign::factory()->create()->id,
            'created_by' => (int)User::factory()->create()->id,
            'description' => null,
            'game_end' => null,
            'game_start' => null,
            'name' => $this->faker->company(),
            'real_end' => null,
            'real_start' => now()->toDateTimeString(),
        ];
    }
}
