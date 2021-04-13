<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ChatCharacter;
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
            'channel' => Channel::factory()->create(),
            'chat_user' => ChatUser::factory()->create(),
            'character_id' => Character::factory()->create(),
        ];
    }
}
