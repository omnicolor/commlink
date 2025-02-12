<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Stillfleet\Models\Character;

/**
 * @extends Factory<Character>
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
            'charm' => 'd6',
            'combat' => 'd4',
            'movement' => 'd8+1',
            'name' => 'Fake Stillfleet Character',
            'system' => 'stillfleet',
        ];
    }
}
