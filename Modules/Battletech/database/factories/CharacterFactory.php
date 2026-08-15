<?php

declare(strict_types=1);

namespace Modules\Battletech\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Battletech\Models\Character;
use Override;

/**
 * @extends Factory<Character>
 */
class CharacterFactory extends Factory
{
    protected $model = Character::class;

    /**
     * @return array<string, string>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'owner' => $this->faker->email,
            'system' => 'battletech',
        ];
    }
}
