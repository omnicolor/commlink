<?php

declare(strict_types=1);

namespace Database\Factories\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use App\Models\User;
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
     * @return array<string, string>
     */
    public function definition(): array
    {
        return [
            'handle' => $this->faker->name,
            'owner' => (string)(User::factory()->create())->email,
            'system' => 'shadowrun5e',
        ];
    }
}
