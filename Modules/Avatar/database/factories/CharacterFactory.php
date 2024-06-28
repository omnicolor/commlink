<?php

declare(strict_types=1);

namespace Modules\Avatar\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Avatar\Models\Character;

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
     * @return array<string, string>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'owner' => $this->faker->email,
            'system' => 'avatar',
        ];
    }
}
