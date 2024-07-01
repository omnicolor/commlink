<?php

declare(strict_types=1);

namespace Modules\Blistercritters\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Blistercritters\Models\Character;

/**
 * @extends Factory<Character>
 * @psalm-suppress UnusedClass
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
     * Define the model's default state.
     * @return array<string, string>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'owner' => $this->faker->email,
            'system' => 'blistercritters',
        ];
    }
}
