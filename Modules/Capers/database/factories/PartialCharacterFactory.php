<?php

declare(strict_types=1);

namespace Modules\Capers\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Capers\Models\PartialCharacter;

/**
 * @extends Factory<PartialCharacter>
 */
class PartialCharacterFactory extends Factory
{
    protected $model = PartialCharacter::class;

    /**
     * @return array<string, string>
     */
    public function definition(): array
    {
        return [
            'owner' => $this->faker->email,
            'system' => 'capers',
        ];
    }
}
