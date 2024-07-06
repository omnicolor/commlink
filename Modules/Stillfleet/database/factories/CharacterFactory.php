<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Stillfleet\Models\Character;

/**
 * @extends Factory<Character>
 * @psalm-suppress UnusedClass
 */
class CharacterFactory extends Factory
{
    protected $model = Character::class;

    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Fake Stillfleet Character',
            'system' => 'stillfleet',
        ];
    }
}
