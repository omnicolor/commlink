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
        $mock = $this->getStubBuilder(Roll::class)
            ->setConstructorArgs(['', 'username', new Channel()])
            ->getStub();

        self::assertFalse($mock->isGm());
    }

    public function testIsGmNoChatUser(): void
    {
        $campaign = Campaign::factory()->create();
        $channel = Channel::factory()->create(['campaign_id' => $campaign]);

        $mock = $this->getStubBuilder(Roll::class)
            ->setConstructorArgs(['', 'username', $channel])
            ->getStub();

        self::assertFalse($mock->isGm());
    }
}
