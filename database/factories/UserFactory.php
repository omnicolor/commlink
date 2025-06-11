<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Override;
use Ramsey\Uuid\Uuid;
use Spatie\Permission\Models\Role;

use function now;
use function str_replace;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * @return array{
     *     name: string,
     *     email: string,
     *     email_verified_at: string,
     *     password: string,
     *     remember_token: string
     * }
     */
    #[Override]
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
     */
    public function unverified(): static
    {
        return $this->state(fn () => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): Factory
    {
        return $this->afterCreating(function (User $user): void {
            $admin = Role::findOrCreate('admin');
            $user->assignRole($admin);
        });
    }
}
