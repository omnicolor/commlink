<?php

declare(strict_types=1);

namespace Modules\Dnd5e\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Dnd5e\Models\Character;

/**
 * @extends Factory<Character>
 * @psalm-suppress UnusedClass
 */
class CharacterFactory extends Factory
{
    protected $model = Character::class;

    /**
     * Define the model's default state.
     * @return array<string, int|string>
     */
    public function definition(): array
    {
        return [
            'charisma' => $this->faker->numberBetween(1, 30),
            'constitution' => $this->faker->numberBetween(1, 30),
            'dexterity' => $this->faker->numberBetween(1, 30),
            'intelligence' => $this->faker->numberBetween(1, 30),
            'name' => $this->faker->name,
            'owner' => $this->faker->email,
            'strength' => $this->faker->numberBetween(1, 30),
            'system' => 'dnd5e',
            'wisdom' => $this->faker->numberBetween(1, 30),
        ];
    }
}
