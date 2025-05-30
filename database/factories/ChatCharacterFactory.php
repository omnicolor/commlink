<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Override;

/**
 * @extends Factory<ChatCharacter>
 */
class ChatCharacterFactory extends Factory
{
    /**
     * @return array{
     *     channel_id: int,
     *     chat_user_id: int
     * }
     */
    #[Override]
    public function definition(): array
    {
        return [
            'channel_id' => Channel::factory()->create()->id,
            'chat_user_id' => (int)ChatUser::factory()->create()->id,
        ];
    }
}
