<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Events\RollEvent;
use App\Models\Slack\Channel;

/**
 * Tests for RollEvents.
 * @group events
 */
final class RollEventTest extends \Tests\TestCase
{
    /**
     * Test the constructor.
     * @test
     */
    public function testConstructor(): void
    {
        $channel = Channel::factory()->make();
        $event = new RollEvent('Title', 'Text', [1, 2, 3], $channel);
        self::assertSame('Title', $event->title);
        self::assertSame('Text', $event->text);
        self::assertSame([1, 2, 3], $event->rolls);
        self::assertEquals($channel, $event->source);
    }
}
