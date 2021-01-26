<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Character;
use Illuminate\Database\Eloquent\Factories\Factory;

class CharacterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = Character::class;

    /**
     * Types of characters that Commlink supports.
     * @var string[]
     */
    protected array $types = [
        'cyberpunkred',
        'expanse',
        'paranoia',
        'shadowruna',
        'shadowrun5e',
        'shadowrun6e',
        'startrekadventures',
    ];

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'handle' => $this->faker->name,
            'owner' => $this->faker->email,
            'type' => $this->faker->randomElement($this->types),
        ];
    }
}
