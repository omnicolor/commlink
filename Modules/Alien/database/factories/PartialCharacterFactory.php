<?php

declare(strict_types=1);

namespace Modules\Alien\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Alien\Models\PartialCharacter;
use Override;

/**
 * @extends Factory<PartialCharacter>
 */
class PartialCharacterFactory extends Factory
{
    protected $model = PartialCharacter::class;

    /**
     * @return array<string, string>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'owner' => $this->faker->email,
            'system' => 'alien',
        ];
    }
}
