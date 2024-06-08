<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls;

use App\Models\Campaign;
use App\Models\Channel;
use App\Rolls\Roll;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Medium]
final class RollTest extends TestCase
{
    public function testIsGmNoCampaign(): void
    {
        $mock = $this->getMockBuilder(Roll::class)
            ->setConstructorArgs(['', 'username', new Channel()])
            ->getMock();

        self::assertFalse($mock->isGm());
    }

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
