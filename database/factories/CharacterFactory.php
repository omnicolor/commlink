<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Character;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Override;

use function array_keys;
use function config;
use function in_array;

/**
 * @extends Factory<Character>
 */
class CharacterFactory extends Factory
{
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
     * @return array{
     *     handle: string,
     *     name: string,
     *     owner: string,
     *     system: string
     * }
     */
    #[Override]
    public function definition(): array
    {
        $name = $this->faker->name;
        return [
            'handle' => $name,
            'name' => $name,
            'owner' => (User::factory()->create())->email->address,
            'system' => (string)$this->faker->randomElement(
                array_keys((array)config('commlink.systems'))
            ),
        ];
    }

    /**
     * Configure the model factory to properly set the handle or name depending
     * on the system.
     */
    #[Override]
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
