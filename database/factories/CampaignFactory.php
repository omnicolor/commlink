<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CampaignFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = Campaign::class;

    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->create();
        return [
            'name' => $this->faker->company(),
            'description' => $this->faker->text(),
            'registered_by' => $user,
            'gm' => $user,
            'system' => $this->faker->randomElement(
                \array_keys(config('app.systems'))
            ),
            'options' => '{"start-date":"2080-01-01","gameplay":"established"}',
        ];
    }
}
