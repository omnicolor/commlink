<?php

declare(strict_types=1);

namespace Database\Factories\Transformers;

use App\Models\Transformers\PartialCharacter;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'system' => 'transformers',
        ];
    }
}
