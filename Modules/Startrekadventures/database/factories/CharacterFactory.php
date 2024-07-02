<?php

declare(strict_types=1);

namespace Modules\Startrekadventures\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Startrekadventures\Models\Character;

/**
 * @extends Factory<Character>
 */
class CharacterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     * @phpstan-ignore-next-line
     * @var mixed
     */
    protected $model = Character::class;

    /**
     * @return array<string, string|array<int|string, int|string>>
     */
    public function definition(): array
    {
        return [
            'attributes' => [
                'control' => $this->faker->numberBetween(7, 12),
                'daring' => $this->faker->numberBetween(7, 12),
                'fitness' => $this->faker->numberBetween(7, 12),
                'insight' => $this->faker->numberBetween(7, 12),
                'presence' => $this->faker->numberBetween(7, 12),
                'reason' => $this->faker->numberBetween(7, 12),
            ],
            'disciplines' => [
                'command' => $this->faker->numberBetween(0, 5),
                'conn' => $this->faker->numberBetween(0, 5),
                'engineering' => $this->faker->numberBetween(0, 5),
                'medicine' => $this->faker->numberBetween(0, 5),
                'science' => $this->faker->numberBetween(0, 5),
                'security' => $this->faker->numberBetween(0, 5),
            ],
            'name' => $this->faker->name,
            'owner' => $this->faker->email,
            'species' => 'Human',
            'system' => 'startrekadventures',
        ];
    }
}
