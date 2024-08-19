<?php

declare(strict_types=1);

namespace Modules\Avatar\Database\Factories;

use Database\Factories\Factory;
use Modules\Avatar\Models\Campaign;

/**
 * @extends Factory<Campaign>
 * @method mixed hasInitiatives(int $count)
 * @psalm-suppress UnusedClass
 */
class CampaignFactory extends Factory
{
    protected $model = Campaign::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'description' => $this->faker->text(),
            'registered_by' => $this->createUser(),
            'gm' => $this->createUser(),
            'system' => 'avatar',
            'options' => [
                'era' => 'aang',
            ],
        ];
    }
}
