<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Channel;
use App\Models\Character;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Tests for the ChatCharacter class.
 * @group discord
 * @group models
 * @group slack
 * @medium
 */
final class ChatCharacterTest extends \Tests\TestCase
{
    use RefreshDatabase;

    /**
     * Test returning the relationships.
     * @test
     */
    public function testRelationships(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->create();
        /** @var Character */
        $character = Character::factory()->create();
        /** @var ChatUser */
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
