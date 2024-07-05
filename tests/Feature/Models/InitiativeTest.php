<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Campaign;
use App\Models\Channel;
use App\Models\Initiative;
use Modules\Shadowrun5e\Models\Character;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

/**
 * Tests for the Initiative model.
 */
#[Medium]
final class InitiativeTest extends TestCase
{
    public function testToStringWithoutCharacter(): void
    {
        $initiative = new Initiative(['character_name' => 'Conan']);
        self::assertSame('Conan', (string)$initiative);
    }

    public function testToStringWithCharacter(): void
    {
        /** @var Character */
        $character = Character::factory()->create([
            'created_by' => self::class . '::' . __FUNCTION__,
        ]);

        $initiative = new Initiative(['character_id' => $character->id]);
        self::assertSame($character->handle, (string)$initiative);

        $character->delete();
    }

    public function testCampaign(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create();

        $initiative = new Initiative([
            'campaign_id' => $campaign->id,
        ]);

        self::assertSame(
            $campaign->name,
            optional($initiative->campaign)->name
        );
    }

    public function testScopeForCampaign(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create();

        Initiative::factory()->count(4)->create(['campaign_id' => $campaign]);

        self::assertCount(4, Initiative::forCampaign($campaign)->get());
    }

    public function testScopeForChannel(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make();

        Initiative::forChannel($channel)->delete();
        Initiative::factory()->count(3)->create([
            'campaign_id' => null,
            'channel_id' => $channel->id,
        ]);

        self::assertCount(3, Initiative::forChannel($channel)->get());
    }
}
