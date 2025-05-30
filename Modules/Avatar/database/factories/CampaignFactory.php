<?php

declare(strict_types=1);

namespace Modules\Avatar\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Avatar\Models\Campaign;
use Override;

/**
 * @extends Factory<Campaign>
 * @method mixed hasInitiatives(int $count)
 */
class CampaignFactory extends Factory
{
    protected $model = Campaign::class;

    /**
     * @return array{
     *     name: string,
     *     description: string,
     *     registered_by: int,
     *     gm: int,
     *     system: string,
     *     options: array{
     *         era: string
     *     }
     * }
     */
    #[Override]
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'description' => $this->faker->text(),
            'registered_by' => User::factory()->create()->id,
            'gm' => User::factory()->create()->id,
            'system' => 'avatar',
            'options' => [
                'era' => 'aang',
            ],
        ];
    }
}
