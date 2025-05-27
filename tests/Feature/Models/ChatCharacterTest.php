<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Channel;
use App\Models\Character;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('discord')]
#[Group('slack')]
#[Medium]
final class ChatCharacterTest extends TestCase
{
    /**
     * Test returning the relationships.
     */
    public function testRelationships(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->create();
        $character = Character::factory()->create([
        ]);
        $chatUser = ChatUser::factory()->create();

        $chatCharacter = new ChatCharacter([
            'channel_id' => $channel->id,
            'character_id' => $character->_id,
            'chat_user_id' => $chatUser->id,
        ]);

        self::assertInstanceOf(Channel::class, $chatCharacter->channel);
        self::assertInstanceOf(
            Character::class,
            $chatCharacter->getCharacter()
        );
        self::assertInstanceOf(ChatUser::class, $chatCharacter->chatUser);

        $character->delete();
    }
}
