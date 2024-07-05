<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Events\InitiativeAdded;
use App\Models\Campaign;
use App\Models\Initiative;
use Modules\Cyberpunkred\Models\Character;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('events')]
#[Medium]
final class InitiativeAddedTest extends TestCase
{
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

        self::assertSame($campaign, $event->campaign);
        self::assertSame($initiative, $event->initiative);
    }
}
