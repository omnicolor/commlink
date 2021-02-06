<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Slack;

use App\Models\Slack\Channel;

/**
 * Tests for Slack channels.
 * @covers \App\Models\Slack\Channel
 * @group models
 * @group slack
 */
final class ChannelTest extends \Tests\TestCase
{
    /**
     * Test the accessor on an unregistered channel.
     * @test
     */
    public function testGettingSystemUnregistered(): void
    {
        $channel = new Channel();
        self::assertSame('unregistered', $channel->system);
    }

    /**
     * Test the accessor on a registered channel.
     * @test
     */
    public function testGettingSystemRegistered(): void
    {
        $channel = new Channel(['system' => 'cyberpunkred']);
        self::assertSame('cyberpunkred', $channel->system);
    }
}
