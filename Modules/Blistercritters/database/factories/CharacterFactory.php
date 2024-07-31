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
    protected $model = Character::class;

    /**
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
