<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Character;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

use function array_keys;
use function config;
use function in_array;

/**
 * @extends Factory<Character>
 */
class CharacterFactory extends Factory
{
    protected $model = Character::class;

    /**
     * List of systems that use 'handle' instead of 'name' to describe the
     * character.
     * @var array<int, string>
     */
    protected array $uses_handle = [
        'cyberpunkred',
        'shadowrunanarchy',
        'shadowrun5e',
        'shadowrun6e',
    ];

    /**
     * Define the model's default state.
     * @return array<string, string>
     */
    public function definition(): array
    {
        $name = $this->faker->name;
        return [
            'handle' => $name,
            'name' => $name,
            'owner' => (string)(User::factory()->create())->email,
            'system' => (string)$this->faker->randomElement(
                array_keys((array)config('commlink.systems'))
            ),
        ];
    }

    /**
     * Configure the model factory to properly set the handle or name depending
     * on the system.
     */
    public function configure(): CharacterFactory
    {
        $updateName = function (Model $character): void {
            if (in_array($character->system, $this->uses_handle, true)) {
                $character->name = null;
                return;
            }

            $character->handle = null;
        };

        return $this->afterMaking($updateName)
            ->afterCreating(function (Model $character) use ($updateName): void {
                $updateName($character);
                $character->save();
            });
    }
}
