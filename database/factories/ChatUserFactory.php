<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ChatUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ChatUser>
 */
class ChatUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = ChatUser::class;

    /**
     * Define the model's default state.
     * @return array<string, bool|int|string>
     */
    public function definition(): array
    {
        return [
            'server_id' => Str::random(10),
            'server_name' => $this->faker->company(),
            'server_type' => $this->faker->randomElement(ChatUser::VALID_TYPES),
            'remote_user_id' => Str::random(10),
            'remote_user_name' => $this->faker->name(),
            'user_id' => User::factory()->create(),
            'verified' => $this->faker->boolean(),
        ];
    }
}
