<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Shadowrun5e\Models\PartialCharacter;
use Override;

/**
 * @extends Factory<PartialCharacter>
 */
class PartialCharacterFactory extends Factory
{
    protected $model = PartialCharacter::class;

    /**
     * @return array{
     *     owner: string,
     *     system: string
     * }
     */
    #[Override]
    public function definition(): array
    {
        return [
            'owner' => $this->faker->email,
            'system' => 'shadowrun5e',
        ];
    }
}
