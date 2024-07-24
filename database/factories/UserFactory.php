<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

/**
 * @extends Factory<User>
 * @psalm-suppress UnusedClass
 */
class UserFactory extends Factory
{
    /**
     * @var mixed
     */
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => str_replace(
                '@',
                '#' . Uuid::uuid4() . '@',
                $this->faker->unique()->email,
            ),
            'email_verified_at' => now()->toDateTimeString(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function unverified(): static
    {
        return $this->state(fn () => [
            'email_verified_at' => null,
        ]);
    }
}
