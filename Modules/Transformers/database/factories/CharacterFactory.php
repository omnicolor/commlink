<?php

declare(strict_types=1);

namespace Modules\Transformers\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Transformers\Models\Character;
use Modules\Transformers\Models\Mode;
use Modules\Transformers\Models\Programming;
use Modules\Transformers\Models\Size;

/**
 * @extends Factory<Character>
 */
class CharacterFactory extends Factory
{
    protected $model = Character::class;

    /**
     * @return array<string, array|int|string>
     */
    public function definition(): array
    {
        return [
            'allegiance' => $this->faker->randomElement([
                'Autobots',
                'Decepticons',
            ]),
            'alt_mode' => $this->faker->randomElement([
                'Car',
                'Small Vehicle',
                'Race Car',
                'Fire Truck',
            ]),
            'color_primary' => 'Red',
            'color_secondary' => 'Blue',
            'courage_alt' => $this->faker->numberBetween(1, 10),
            'courage_robot' => $this->faker->numberBetween(1, 10),
            'endurance_alt' => $this->faker->numberBetween(1, 10),
            'endurance_robot' => $this->faker->numberBetween(1, 10),
            'firepower_alt' => $this->faker->numberBetween(1, 10),
            'firepower_robot' => $this->faker->numberBetween(1, 10),
            'intelligence_alt' => $this->faker->numberBetween(1, 10),
            'intelligence_robot' => $this->faker->numberBetween(1, 10),
            'mode' => strtolower($this->faker->randomElement(Mode::class)->value),
            'name' => $this->faker->name,
            'owner' => $this->faker->email,
            'programming' => $this->faker->randomElement(Programming::class),
            'rank' => $this->faker->numberBetween(1, 10),
            'size' => $this->faker->randomElement(Size::class),
            'skill_alt' => $this->faker->numberBetween(1, 10),
            'skill_robot' => $this->faker->numberBetween(1, 10),
            'speed_alt' => $this->faker->numberBetween(1, 10),
            'speed_robot' => $this->faker->numberBetween(1, 10),
            'strength_alt' => $this->faker->numberBetween(1, 10),
            'strength_robot' => $this->faker->numberBetween(1, 10),
            'system' => 'transformers',
            'weapons' => [],
        ];
    }
}
