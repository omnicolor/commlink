<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Events\DiscordUserLinked;
use App\Models\ChatUser;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for the event fired when a Discord user gets linked.
 * @group discord
 * @group events
 * @medium
 */
final class DiscordUserLinkedTest extends TestCase
{
    use RefreshDatabase;

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
