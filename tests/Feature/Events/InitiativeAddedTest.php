<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Events\InitiativeAdded;
use App\Models\Campaign;
use App\Models\Cyberpunkred\Character;
use App\Models\Initiative;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for initiative getting added and broadcast.
 * @group events
 * @medium
 */
final class InitiativeAddedTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the constructor.
     * @test
     */
    public function testConstructor(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->make(['system' => 'cyberpunkred']);

        /** @var Character */
        $character = Character::factory()->make();

        /** @var Initiative */
        $initiative = Initiative::factory()->make([
            'campaign_id' => $campaign,
            'character_id' => $character->id,
        ]);

        $event = new InitiativeAdded($initiative, $campaign, null);

        self::assertInstanceOf(PrivateChannel::class, $event->broadcastOn());
        self::assertSame($campaign, $event->campaign);
        self::assertSame($initiative, $event->initiative);
    }
}
