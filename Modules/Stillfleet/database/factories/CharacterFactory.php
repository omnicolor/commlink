<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Stillfleet\Models\Character;
use Override;

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
    #[Override]
    public function definition(): array
    {
        return [
            'charm' => 'd6',
            'combat' => 'd4',
            'movement' => 'd8',
            'will' => 'd10',
            'name' => 'Fake Stillfleet Character',
            'system' => 'stillfleet',
        ];
    }
}
