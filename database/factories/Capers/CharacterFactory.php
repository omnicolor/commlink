<?php

declare(strict_types=1);

namespace Database\Factories\Capers;

use App\Models\Capers\Character;
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
        $types = [
            Character::TYPE_CAPER,
            Character::TYPE_EXCEPTIONAL,
            Character::TYPE_REGULAR,
        ];

        $resilience = $this->faker->numberBetween(1, 5);
        $charisma = $this->faker->numberBetween(1, 5);
        $maxHits = 4 + (2 * $resilience) + (2 * $charisma);

        $identities = require config('app.data_path.capers') . 'identities.php';
        $skills = require config('app.data_path.capers') . 'skills.php';
        $vices = require config('app.data_path.capers') . 'vices.php';
        $virtues = require config('app.data_path.capers') . 'virtues.php';

        $type = $this->faker->randomElement($types);
        $typeSpecific = [];
        if (Character::TYPE_CAPER === $type) {
            $powers = require config('app.data_path.capers') . 'powers.php';
            $power = $this->faker->randomElement($powers);
            $typeSpecific['powers'][] = [
                'id' => $this->nameToId($power['name']),
            ];
        } elseif (Character::TYPE_EXCEPTIONAL === $type) {
            $perks = require config('app.data_path.capers') . 'perks.php';
            $perk = $this->faker->randomElement(array_keys($perks));
            $perk = $this->nameToId($perk);
            if ('specialty-skill' === $perk) {
                $typeSpecific['perks'][] = [
                    'id' => $perk,
                    'skill' => $this->faker->randomElement(array_keys($skills)),
                ];
            } else {
                $typeSpecific['perks'][] = [
                    'id' => $perk,
                ];
            }
        }

        return array_merge(
            [
                'agility' => $this->faker->numberBetween(1, 5),
                'background' => $this->faker->text(),
                'charisma' => $charisma,
                'description' => $this->faker->text(),
                'expertise' => $this->faker->numberBetween(1, 5),
                'hits' => $this->faker->numberBetween(0, $maxHits),
                'identity' => $this->faker->randomElement(array_keys($identities)),
                'level' => $this->faker->numberBetween(1, 10),
                'mannerisms' => $this->faker->text(),
                'moxie' => 3,
                'name' => $this->faker->name(),
                'perception' => $this->faker->numberBetween(1, 5),
                'resilience' => $resilience,
                'skills' => $this->faker->randomElements(
                    array_keys($skills),
                    $this->faker->numberBetween(1, 8)
                ),
                'strength' => $this->faker->numberBetween(1, 5),
                'system' => 'capers',
                'type' => $type,
                'vice' => $this->faker->randomElement(array_keys($vices)),
                'virtue' => $this->faker->randomElement(array_keys($virtues)),
            ],
            $typeSpecific
        );
    }

    protected function nameToId(string $name): string
    {
        return str_replace(' ', '-', strtolower($name));
    }
}
