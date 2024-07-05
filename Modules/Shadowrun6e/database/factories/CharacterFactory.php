<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Shadowrun6e\Models\Character;

/**
 * @extends Factory<Character>
 */
class CharacterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Character::class;

    /**
     * Define the model's default state.
     * @return array<string, int|string>
     */
    public function definition(): array
    {
        return [
            'agility' => 3,
            'body' => 3,
            'charisma' => 3,
            'handle' => $this->faker->name,
            'intuition' => 3,
            'karma' => 0,
            'karma_total' => 0,
            'logic' => 3,
            'name' => $this->faker->name,
            'owner' => $this->faker->email,
            'reaction' => 3,
            'strength' => 3,
            'system' => 'shadowrun6e',
            'willpower' => 3,
        ];
    }
}
