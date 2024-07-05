<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Cyberpunkred\Models\Character;

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
        $hp = $this->faker->randomDigitNotNull() * $this->faker->randomDigitNotNull();
        return [
            'handle' => $this->faker->name,
            'owner' => $this->faker->email,
            'system' => 'cyberpunkred',
            'body' => $this->faker->randomDigitNotNull(),
            'cool' => $this->faker->randomDigitNotNull(),
            'dexterity' => $this->faker->randomDigitNotNull(),
            'empathy' => $this->faker->randomDigitNotNull(),
            'hitPointsCurrent' => $hp - $this->faker->randomDigit(),
            'hitPointsMax' => $hp,
            'intelligence' => $this->faker->randomDigitNotNull(),
            'luck' => $this->faker->randomDigitNotNull(),
            'movement' => $this->faker->randomDigitNotNull(),
            'reflexes' => $this->faker->randomDigitNotNull(),
            'technique' => $this->faker->randomDigitNotNull(),
            'willpower' => $this->faker->randomDigitNotNull(),
        ];
    }
}
