<?php

declare(strict_types=1);

namespace Database\Factories\Expanse;

use App\Models\Expanse\Character;
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
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'owner' => $this->faker->email,
            'system' => 'expanse',
        ];
    }
}
