<?php

declare(strict_types=1);

namespace Database\Factories\Cyberpunkred;

use App\Models\Cyberpunkred\Character;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Character>
 */
class CharacterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     * @var string
     */
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
