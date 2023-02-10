<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChatCharacter>
 */
class ChatCharacterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = ChatCharacter::class;

    /**
     * Define the model's default state.
     * @return array<string, int>
     */
    public function definition(): array
    {
        return [
            'channel_id' => Channel::factory()->create()->id,
            'chat_user_id' => ChatUser::factory()->create()->id,
        ];
    }
}
