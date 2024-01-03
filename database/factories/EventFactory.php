<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = Event::class;

    /**
     * @return array<string, int|null|string>
     */
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
            'real_start' => now(),
        ];
    }
}
