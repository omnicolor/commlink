<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Campaign;
use App\Models\Channel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Tests for the campaign model.
 * @group campaigns
 * @group models
 * @medium
 */
final class CampaignTest extends \Tests\TestCase
{
    use RefreshDatabase;

    /**
     * Test getting the users associated with the campaign.
     * @test
     */
    public function testGetUsers(): void
    {
        /** @var User */
        $gm = User::factory()->create();
        /** @var User */
        $registerer = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'gm' => $gm->id,
            'registered_by' => $registerer->id,
        ]);
        // @phpstan-ignore-next-line
        self::assertSame($gm->id, $campaign->gamemaster->id);
        // @phpstan-ignore-next-line
        self::assertSame($registerer->id, $campaign->registeredBy->id);
    }

    /**
     * Test that the GM for a campaign can be null.
     * @test
     */
    public function testNullGM(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create(['gm' => null]);
        self::assertNull($campaign->gamemaster);
    }

    /**
     * Test that trying to set an invalid system throws an exception.
     * @test
     */
    public function testSetInvalidSystem(): void
    {
        $campaign = new Campaign();
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Invalid system');
        $campaign->system = 'foo';
    }

    /**
     * Test setting the system to a valid value sets it.
     * @test
     */
    public function testSetSystem(): void
    {
        $system = \key(config('app.systems'));
        $campaign = new Campaign();
        $campaign->system = $system;
        self::assertSame($system, $campaign->system);
    }

    /**
     * Test getting the campaign's channels when none have been set.
     * @test
     */
    public function testGetChannelsNone(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->make();
        self::assertCount(0, $campaign->channels);
    }

    /**
     * Test getting the campaign's channels when it has several.
     * @test
     */
    public function testGetChannels(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create();
        Channel::factory()->count(2)->create([
            'campaign_id' => $campaign,
        ]);
        self::assertCount(2, $campaign->channels);
    }
}
