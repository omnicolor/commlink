<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChatCharacter>
 * @psalm-suppress UnusedClass
 */
class ChatCharacterFactory extends Factory
{
    /**
     * @var mixed
     */
    protected $model = ChatCharacter::class;

    /**
     * Define the model's default state.
     * @return array<string, int>
     */
    public function definition(): array
    {
        return [
            'channel_id' => Channel::factory()->create([
                'type' => Channel::TYPE_SLACK,
            ])->id,
            'chat_user_id' => (int)ChatUser::factory()->create()->id,
        ];
    }
}
