<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Shadowrun5e\Models\PartialCharacter;

/**
 * @extends Factory<PartialCharacter>
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
            'system' => 'shadowrun5e',
        ];
    }
}
