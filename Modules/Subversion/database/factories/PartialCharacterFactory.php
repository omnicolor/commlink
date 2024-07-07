<?php

declare(strict_types=1);

namespace Modules\Subversion\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Subversion\Models\PartialCharacter;

/**
 * @extends Factory<PartialCharacter>
 * @psalm-suppress UnusedClass
 */
class PartialCharacterFactory extends Factory
{
    protected $model = PartialCharacter::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner' => $this->faker->email,
            'system' => 'subversion',
        ];
    }
}
