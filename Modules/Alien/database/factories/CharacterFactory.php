<?php

declare(strict_types=1);

namespace Modules\Alien\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Alien\Models\Character;
use Override;

/**
 * @extends Factory<Character>
 */
class CharacterFactory extends Factory
{
    protected $model = Character::class;

    /**
     * @return array<string, int|string>
     */
    #[Override]
    public function definition(): array
    {
        $strength = $this->faker->numberBetween(1, 5);
        return [
            'agility' => $this->faker->numberBetween(1, 5),
            'empathy' => $this->faker->numberBetween(1, 5),
            'health_current' => $strength,
            'owner' => $this->faker->email,
            'name' => $this->faker->name(),
            'strength' => $strength,
            'wits' => $this->faker->numberBetween(1, 5),
        ];
    }
}
