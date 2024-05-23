<?php

declare(strict_types=1);

namespace Database\Factories\Subversion;

use App\Models\Subversion\PartialCharacter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PartialCharacter>
 */
class PartialCharacterFactory extends Factory
{
    /**
     * @var string
     */
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
