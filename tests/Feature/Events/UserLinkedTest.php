<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Events\UserLinked;
use App\Models\ChatUser;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function sprintf;

#[Group('discord')]
#[Group('events')]
#[Medium]
final class UserLinkedTest extends TestCase
{
    public function testBroadcast(): void
    {
        /** @var ChatUser */
        $chatUser = ChatUser::factory()->make();
        $event = new UserLinked($chatUser);
        $channel = $event->broadcastOn();
        self::assertSame(
            sprintf('private-users.%d', $chatUser->user_id),
            $channel->name
        );
    }
}
