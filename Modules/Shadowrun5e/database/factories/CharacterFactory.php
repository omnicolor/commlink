<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Shadowrun5e\Models\Character;
use Override;

/**
 * @extends Factory<Character>
 */
class CharacterFactory extends Factory
{
    protected $model = Character::class;

    /**
     * @return array{
     *     handle: string,
     *     owner: string,
     *     system: string
     * }
     */
    #[Override]
    public function definition(): array
    {
        return [
            'handle' => $this->faker->name,
            'owner' => (string)(User::factory()->create())->email,
            'system' => 'shadowrun5e',
        ];
    }
}
