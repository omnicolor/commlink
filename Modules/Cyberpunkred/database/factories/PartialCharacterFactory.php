<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Cyberpunkred\Models\PartialCharacter;

/**
 * @extends Factory<PartialCharacter>
 * @psalm-suppress UnusedClass
 */
class PartialCharacterFactory extends Factory
{
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
