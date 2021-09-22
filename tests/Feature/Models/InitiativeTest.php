<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Campaign;
use App\Models\Channel;
use App\Models\Initiative;
use App\Models\Shadowrun5E\Character;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Tests for the Initiative model.
 * @group models
 * @medium
 */
final class InitiativeTest extends \Tests\TestCase
{
    use RefreshDatabase;

    public function testToStringWithoutCharacter(): void
    {
        $initiative = new Initiative(['character_name' => 'Conan']);
        self::assertSame('Conan', (string)$initiative);
    }

    public function testToStringWithCharacter(): void
    {
        /** @var Character */
        $character = Character::factory()->create();

        $initiative = new Initiative(['character_id' => $character->id]);
        self::assertSame($character->handle, (string)$initiative);
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

        Initiative::factory()->count(3)->create([
            'campaign_id' => null,
            'channel_id' => $channel->channel_id,
        ]);

        self::assertCount(3, Initiative::forChannel($channel)->get());
    }
}
