<?php

declare(strict_types=1);

namespace Modules\Capers\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Capers\Models\Character;

use function config;
use function str_replace;
use function strtolower;

/**
 * @extends Factory<Character>
 */
class CharacterFactory extends Factory
{
    protected $model = Character::class;

    /**
     * @return array<string, array<int, string>|int|string>
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

        /**
         * @psalm-suppress UnresolvableInclude
         * @var array<string, array<string, string>>
         */
        $identities = require config('capers.data_path') . 'identities.php';
        /**
         * @psalm-suppress UnresolvableInclude
         * @var array<string, array<string, string>>
         */
        $skills = require config('capers.data_path') . 'skills.php';
        /**
         * @psalm-suppress UnresolvableInclude
         * @var array<string, array<string, string>>
         */
        $vices = require config('capers.data_path') . 'vices.php';
        /**
         * @psalm-suppress UnresolvableInclude
         * @var array<string, array<string, string>>
         */
        $virtues = require config('capers.data_path') . 'virtues.php';

        $type = $this->faker->randomElement($types);
        $typeSpecific = [];
        if (Character::TYPE_CAPER === $type) {
            /**
             * @psalm-suppress UnresolvableInclude
             * @var array<string, array<string, string>>
             */
            $powers = require config('capers.data_path') . 'powers.php';
            $power = (array)$this->faker->randomElement($powers);
            $typeSpecific['powers'][] = [
                'id' => $this->nameToId($power['name']),
            ];
        } elseif (Character::TYPE_EXCEPTIONAL === $type) {
            /**
             * @psalm-suppress UnresolvableInclude
             * @var array<string, array<string, string>>
             */
            $perks = require config('capers.data_path') . 'perks.php';
            $perk = (string)$this->faker->randomElement(array_keys($perks));
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
