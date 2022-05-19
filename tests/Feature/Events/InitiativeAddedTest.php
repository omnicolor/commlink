<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Events\InitiativeAdded;
use App\Models\Campaign;
use App\Models\CyberpunkRed\Character;
use App\Models\Initiative;
use Illuminate\Broadcasting\PrivateChannel;

/**
 * Tests for initiative getting added and broadcast.
 * @group events
 * @medium
 */
final class InitiativeAddedTest extends \Tests\TestCase
{
    use \phpmock\phpunit\PHPMock;

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
