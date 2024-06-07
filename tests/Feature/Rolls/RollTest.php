<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls;

use App\Models\Campaign;
use App\Models\Channel;
use App\Rolls\Roll;
use Tests\TestCase;

/**
 * Tests for the concrete methods in the base Roll class.
 * @small
 */
final class RollTest extends TestCase
{
    /**
     * Test isGm for a request with no campaign.
     */
    public function testIsGmNoCampaign(): void
    {
        $mock = $this->getMockBuilder(Roll::class)
            ->setConstructorArgs(['', 'username', new Channel()])
            ->getMock();

        self::assertFalse($mock->isGm());
    }

    /**
     * Test isGm for a request that has a campaign but no user.
     * @medium
     */
    public function testIsGmNoChatUser(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create();

        /** @var Channel */
        $channel = Channel::factory()->create(['campaign_id' => $campaign]);

        $mock = $this->getMockBuilder(Roll::class)
            ->setConstructorArgs(['', 'username', $channel])
            ->getMock();

        self::assertFalse($mock->isGm());
    }
}
