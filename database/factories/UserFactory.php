<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;


/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     * @return array<string, string>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => str_replace(
                '@',
                '#' . Uuid::uuid4() . '@',
                $this->faker->unique()->email,
            ),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }
}
