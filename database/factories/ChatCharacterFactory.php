<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Channel;
use App\Models\Character;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatCharacterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = ChatCharacter::class;

    /**
     * Define the model's default state.
     * @return array
     */
    public function definition(): array
    {
        return [
            'channel_id' => Channel::factory()->create(),
            'chat_user_id' => ChatUser::factory()->create(),
            'character_id' => Character::factory()->create(),
        ];
    }
}
