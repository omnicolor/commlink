<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Character;
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
     * @return array
     */
    public function definition(): array
    {
        $system = $this->faker->randomElement(\array_keys(config('app.systems')));
        if (in_array($system, ['cyberpunkred', 'shadowrun5e'], true)) {
            $name = ['handle' => $this->faker->name];
        } else {
            $name = ['name' => $this->faker->name];
        }
        return array_merge(
            $name,
            [
                'owner' => $this->faker->safeEmail,
                'system' => $system,
            ]
        );
    }
}
