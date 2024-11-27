<?php

declare(strict_types=1);

namespace Modules\Root\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Root\Models\Character;

/**
 * @extends Factory<Character>
 */
class CharacterFactory extends Factory
{
    protected $model = Character::class;

    /**
     * @return array{
     *   charm: int,
     *   cunning: int,
     *   finese: int,
     *   luck: int,
     *   might: int,
     *   name: string,
     *   system: string
     * }
     */
    public function definition(): array
    {
        return [
            'charm' => $this->faker->numberBetween(-1, 2),
            'cunning' => $this->faker->numberBetween(-1, 2),
            'finese' => $this->faker->numberBetween(-1, 2),
            'luck' => $this->faker->numberBetween(-1, 2),
            'might' => $this->faker->numberBetween(-1, 2),
            'name' => $this->faker->name(),
            'system' => 'root',
        ];
    }
}
