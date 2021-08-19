<?php

declare(strict_types=1);

namespace Tests\Feature\Event;

use App\Events\DiscordUserLinked;
use App\Models\ChatUser;
use Illuminate\Broadcasting\PrivateChannel;

/**
 * Tests for the event fired when a Discord user gets linked.
 * @group discord
 * @group events
 * @small
 */
final class DiscordUserLinkedTest extends \Tests\TestCase
{
    public function testBroadcast(): void
    {
        /** @var ChatUser */
        $chatUser = ChatUser::factory()->make();
        $event = new DiscordUserLinked($chatUser);
        $channel = $event->broadcastOn();
        self::assertInstanceOf(PrivateChannel::class, $channel);
        self::assertSame(
            sprintf('private-users.%d', $chatUser->user_id),
            $channel->name
        );
    }
}
