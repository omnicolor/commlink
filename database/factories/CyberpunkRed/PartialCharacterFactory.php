<?php

declare(strict_types=1);

namespace Database\Factories\CyberpunkRed;

use App\Models\CyberpunkRed\PartialCharacter;
use Illuminate\Database\Eloquent\Factories\Factory;

class PartialCharacterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = PartialCharacter::class;

    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner' => $this->faker->email,
            'system' => 'cyberpunkred',
        ];
    }
}
