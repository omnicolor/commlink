<?php

declare(strict_types=1);

namespace Database\Factories\Avatar;

use App\Models\Avatar\Character;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'name' => $this->faker->name,
            'owner' => $this->faker->email,
            'system' => 'avatar',
        ];
    }
}
