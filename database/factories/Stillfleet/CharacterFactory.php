<?php

declare(strict_types=1);

namespace Database\Factories\Stillfleet;

use App\Models\Stillfleet\Character;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Character>
 */
class CharacterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = Character::class;

    /**
     * Define the model's default state.
     * @return array<string, array<int, string>,int|string>
     */
    public function definition(): array
    {
        return [
            'name' => 'Fake Stillfleet Character',
            'system' => 'stillfleet',
        ];
    }
}
