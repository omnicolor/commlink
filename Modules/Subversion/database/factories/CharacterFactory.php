<?php

declare(strict_types=1);

namespace Modules\Subversion\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Subversion\Models\Character;

/**
 * @extends Factory<Character>
 * @psalm-suppress UnusedClass
 */
class CharacterFactory extends Factory
{
    protected $model = Character::class;

    /**
     * Define the model's default state.
     * @return array<string, int|string>
     */
    public function definition(): array
    {
        return [
            'agility' => $this->faker->numberBetween(0, 7),
            'arts' => 0,
            'awareness' => $this->faker->numberBetween(0, 7),
            'background' => 'agriculturist',
            'brawn' => $this->faker->numberBetween(0, 7),
            'caste' => $this->faker->randomElement([
                'undercity',
                'lower',
                'lower-middle',
                'upper-middle',
                'upper',
                'elite',
            ]),
            'charisma' => $this->faker->numberBetween(0, 7),
            'lineage' => 'dwarven',
            'lineage_option' => $this->faker->randomElement([
                'lessons-from-the-ground',
                'monstrous-heritage',
                'small',
                'toxin-resistant',
            ]),
            'name' => $this->faker->name,
            'origin' => 'altaipheran',
            'owner' => $this->faker->email,
            'system' => 'subversion',
            'will' => $this->faker->numberBetween(0, 7),
            'wit' => $this->faker->numberBetween(0, 7),
        ];
    }
}
