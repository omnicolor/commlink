<?php

declare(strict_types=1);

namespace Modules\Avatar\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Avatar\Enums\Background;
use Modules\Avatar\Enums\Era;
use Modules\Avatar\Models\Character;
use Override;

/**
 * @extends Factory<Character>
 */
class CharacterFactory extends Factory
{
    protected $model = Character::class;

    /**
     * @return array{
     *     name: string,
     *     appearance: string,
     *     background: string,
     *     era: string,
     *     owner: string,
     *     system: string
     * }
     */
    #[Override]
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'appearance' => $this->faker->sentence(),
            'background' => Background::Military->value,
            'era' => Era::Aang->value,
            'owner' => $this->faker->email,
            'system' => 'avatar',
        ];
    }
}
