<?php

declare(strict_types=1);

namespace Database\Factories\Dnd5e;

use App\Models\Dnd5e\Character;
use Illuminate\Database\Eloquent\Factories\Factory;

class CharacterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = Character::class;

    /**
     * Define the model's default state.
     * @return array
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
