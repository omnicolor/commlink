<?php

declare(strict_types=1);

namespace Modules\Alien\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Alien\Models\Character;

/**
 * @extends Factory<Character>
 */
class CharacterFactory extends Factory
{
    protected $model = Character::class;

    /**
     * @return array<string, int|string>
     */
    public function definition(): array
    {
        $strength = $this->faker->numberBetween(1, 5);
        return [
            'agility' => $this->faker->numberBetween(1, 5),
            'empathy' => $this->faker->numberBetween(1, 5),
            'health_current' => $strength,
            'name' => $this->faker->name(),
            'strength' => $strength,
            'wits' => $this->faker->numberBetween(1, 5),
        ];
    }
}
