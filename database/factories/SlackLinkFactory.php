<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SlackLink;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

class SlackLinkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = SlackLink::class;

    /**
     * Define the model's default state.
     * @return array<int, string|int>
     */
    public function definition(): array
    {
        return [
            'slack_team' => Str::random(10),
            'slack_user' => Str::random(10),
            'character_id' => Str::random(24),
            'user_id' => $this->faker->randomNumber(),
        ];
    }
}
