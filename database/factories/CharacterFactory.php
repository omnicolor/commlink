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
     * List of systems that use 'handle' instead of 'name' to describe the
     * character.
     * @var array<int, string>
     */
    protected array $usesHandle = [
        'cyberpunkred',
        'shadowrun5e',
    ];

    /**
     * Define the model's default state.
     * @return array
     */
    public function definition(): array
    {
        $name = $this->faker->name;
        return [
            'handle' => $name,
            'name' => $name,
            'owner' => $this->faker->safeEmail,
            'system' => $this->faker->randomElement(\array_keys(config('app.systems'))),
        ];
    }

    /**
     * Configure the model factory to properly set the handle or name depending
     * on the system.
     * @return CharacterFactory
     */
    public function configure(): CharacterFactory
    {
        $updateName = function (Character $character): void {
            if (in_array($character->system, $this->usesHandle, true)) {
                $character->name = null;
                return;
            }

            $character->handle = null;
        };

        return $this->afterMaking($updateName)
            ->afterCreating(function (Character $character) use ($updateName): void {
                $updateName($character);
                $character->save();
            });
    }
}
