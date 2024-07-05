<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Shadowrun5e\Models\Character;

/**
 * @extends Factory<Character>
 */
class CharacterFactory extends Factory
{
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
