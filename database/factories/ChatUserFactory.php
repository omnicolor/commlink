<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ChatUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Override;

/**
 * @extends Factory<ChatUser>
 */
class ChatUserFactory extends Factory
{
    /**
     * @return array{
     *     server_id: string,
     *     server_name: string,
     *     server_type: string,
     *     remote_user_id: string,
     *     remote_user_name: string,
     *     user_id: int,
     *     verified: bool
     * }
     */
    #[Override]
    public function definition(): array
    {
        return [
            'server_id' => Str::random(10),
            'server_name' => $this->faker->company(),
            'server_type' => (string)$this->faker
                ->randomElement(ChatUser::VALID_TYPES),
            'remote_user_id' => Str::random(10),
            'remote_user_name' => $this->faker->name(),
            'user_id' => (int)User::factory()->create()->id,
            'verified' => $this->faker->boolean(),
        ];
    }
}
