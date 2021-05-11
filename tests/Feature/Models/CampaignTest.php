<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Campaign;
use App\Models\User;

/**
 * Tests for the campaign model.
 * @group campaigns
 * @group models
 * @small
 */
final class CampaignTest extends \Tests\TestCase
{
    /**
     * Test getting the users associated with the campaign.
     * @test
     */
    public function testGetUsers(): void
    {
        $gm = User::factory()->create();
        $registerer = User::factory()->create();
        $campaign = Campaign::factory()->create([
            'gm' => $gm->id,
            'registered_by' => $registerer->id,
        ]);
        self::assertSame($gm->id, $campaign->gamemaster->id);
        self::assertSame($registerer->id, $campaign->registeredBy->id);
    }

    /**
     * Test that the GM for a campaign can be null.
     * @test
     */
    public function testNullGM(): void
    {
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
}
