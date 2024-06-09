<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\Campaign;
use App\Models\Initiative;
use App\Models\User;
use Facades\App\Services\DiceService;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('initiatives')]
#[Medium]
final class InitiativesControllerTest extends TestCase
{
    /**
     * Test trying to destroy an initiative owned by someone else.
     */
    public function testDestroyAnothersInitiative(): void
    {
        /** @var User */
        $gm = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create([]);

        /** @var Initiative */
        $initiative = Initiative::factory()->create([
            'campaign_id' => $campaign,
        ]);

        self::actingAs($gm)
            ->delete(\sprintf(
                '/api/campaigns/%d/initiatives/%d',
                $campaign->id,
                $initiative->id,
            ))
            ->assertForbidden();
        self::assertModelExists($initiative);
    }

    /**
     * Test trying to destroy an initiative that doesn't exist.
     */
    public function testDestroyNotFound(): void
    {
        /** @var User */
        $gm = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create(['gm' => $gm]);

        self::actingAs($gm)
            ->delete(\sprintf('/api/campaigns/%d/initiatives/0', $campaign->id))
            ->assertNotFound();
    }

    /**
     * Test destroying an initiative.
     */
    public function testDestroy(): void
    {
        /** @var User */
        $gm = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create(['gm' => $gm]);

        /** @var Initiative */
        $initiative = Initiative::factory()->create([
            'campaign_id' => $campaign,
        ]);

        self::actingAs($gm)
            ->delete(\sprintf(
                '/api/campaigns/%d/initiatives/%d',
                $campaign->id,
                $initiative->id,
            ))
            ->assertNoContent();
        self::assertModelMissing($initiative);
    }

    /**
     * Test trying to view initiatives as a non-GM.
     */
    public function testIndexNotGm(): void
    {
        /** @var User */
        $gm = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create([]);

        /** @var Initiative */
        $initiative = Initiative::factory()->create([
            'campaign_id' => $campaign,
        ]);

        self::actingAs($gm)
            ->get(\sprintf('/api/campaigns/%d/initiatives', $campaign->id))
            ->assertForbidden();
    }

    /**
     * Test viewing initiatives for a campaign that has none.
     */
    public function testIndexEmpty(): void
    {
        /** @var User */
        $gm = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create(['gm' => $gm]);

        self::actingAs($gm)
            ->getJson(\sprintf('/api/campaigns/%d/initiatives', $campaign->id))
            ->assertOk()
            ->assertJsonFragment(['initiatives' => []]);
    }

    /**
     * Test viewing initiatives for a campaign.
     */
    public function testIndex(): void
    {
        /** @var User */
        $gm = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create(['gm' => $gm]);

        /** @var Initiative */
        $initiative = Initiative::factory()->create([
            'campaign_id' => $campaign,
        ]);

        $response = self::actingAs($gm)
            ->getJson(\sprintf('/api/campaigns/%d/initiatives', $campaign->id))
            ->assertOk();
        self::assertNotEmpty($response['initiatives']);
    }

    /**
     * Test updating an initiative from a different campaign.
     */
    public function testUpdateDifferentCampaign(): void
    {
        /** @var User */
        $gm = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create(['gm' => $gm]);

        /** @var Initiative */
        $initiative = Initiative::factory()->create([
            'campaign_id' => Campaign::factory()->create(['gm' => $gm]),
        ]);

        self::actingAs($gm)
            ->patchJson(
                \sprintf(
                    '/api/campaigns/%d/initiatives/%d',
                    $campaign->id,
                    $initiative->id,
                ),
                ['initiative' => 5],
            )
            ->assertForbidden();
    }

    /**
     * Test trying to update all of the initiative's fields. Only the
     * initiative and character's name should be editable.
     */
    public function testUpdateInitiative(): void
    {
        /** @var User */
        $gm = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create(['gm' => $gm]);

        /** @var Initiative */
        $initiative = Initiative::factory()->create([
            'campaign_id' => $campaign,
            'character_name' => 'Phil',
            'initiative' => 6,
        ]);

        self::actingAs($gm)
            ->patchJson(
                \sprintf(
                    '/api/campaigns/%d/initiatives/%d',
                    $campaign->id,
                    $initiative->id,
                ),
                [
                    'campaign_id' => $campaign->id + 1,
                    'channel_id' => 'Invalid',
                    'character_name' => 'Punxsutawney Phil',
                    'initiative' => 16,
                ]
            )
            ->assertOk();
        $initiative->refresh();
        self::assertSame($campaign->id, $initiative->campaign_id);
        self::assertNotSame('Invalid', $initiative->channel_id);
        self::assertSame('Punxsutawney Phil', $initiative->character_name);
        self::assertSame(16, $initiative->initiative);
    }

    /**
     * Test trying to view an initiative from a different campaign.
     */
    public function testShowFromDifferentCampaign(): void
    {
        /** @var User */
        $gm = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create(['gm' => $gm]);

        /** @var Initiative */
        $initiative = Initiative::factory()->create([
            'campaign_id' => Campaign::factory()->create(['gm' => $gm]),
        ]);

        self::actingAs($gm)
            ->getJson(\sprintf(
                '/api/campaigns/%d/initiatives/%d',
                $campaign->id,
                $initiative->id,
            ))
            ->assertForbidden();
    }

    /**
     * Test trying to view an initiative.
     */
    public function testShowInitiative(): void
    {
        /** @var User */
        $gm = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create(['gm' => $gm]);

        /** @var Initiative */
        $initiative = Initiative::factory()->create([
            'campaign_id' => $campaign,
        ]);

        $response = self::actingAs($gm)
            ->getJson(\sprintf(
                '/api/campaigns/%d/initiatives/%d',
                $campaign->id,
                $initiative->id,
            ))
            ->assertOk()
            ->assertJson([
                'initiative' => [
                    'id' => $initiative->id,
                    'campaign_id' => $campaign->id,
                    'channel_id' => null,
                    'character_id' => null,
                    'character_name' => (string)$initiative,
                ],
            ]);
    }

    /**
     * Test trying to create an initiative as a non-GM.
     */
    public function testStoreInitiativeNotGm(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create();

        self::actingAs($user)
            ->postJson(
                \sprintf(
                    '/api/campaigns/%d/initiatives',
                    $campaign->id,
                ),
                [
                    'character_name' => 'Testing',
                    'initiative' => 5,
                ],
            )
            ->assertForbidden();
    }

    /**
     * Test creating an initiative setting the initiative directly.
     */
    public function testStoreInitiativeSettingManually(): void
    {
        /** @var User */
        $gm = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create(['gm' => $gm]);

        self::actingAs($gm)
            ->postJson(
                \sprintf(
                    '/api/campaigns/%d/initiatives',
                    $campaign->id,
                ),
                [
                    'character_name' => 'Testing',
                    'initiative' => 5,
                ],
            )
            ->assertCreated()
            ->assertJson([
                'initiative' => [
                    'campaign_id' => $campaign->id,
                    'channel_id' => null,
                    'character_id' => null,
                    'character_name' => 'Testing',
                    'initiative' => 5,
                ],
            ]);
    }

    /**
     * Test creating an initiative setting the initiative base and dice.
     */
    public function testStoreInitiativeBaseAndDice(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(2)
            ->with(6)
            ->andReturn(5);

        /** @var User */
        $gm = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create(['gm' => $gm]);

        self::actingAs($gm)
            ->postJson(
                \sprintf(
                    '/api/campaigns/%d/initiatives',
                    $campaign->id,
                ),
                [
                    'character_name' => 'Testing',
                    'base_initiative' => 9,
                    'initiative_dice' => 2,
                ],
            )
            ->assertCreated()
            ->assertJson([
                'initiative' => [
                    'campaign_id' => $campaign->id,
                    'channel_id' => null,
                    'character_id' => null,
                    'character_name' => 'Testing',
                    'initiative' => 19,
                ],
            ]);
    }

    /**
     * Test trying to remove initiatives for a campaign GMed by another.
     */
    public function testTruncateInitiativesFromAnothersCampaign(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create();

        /** @var Initiative */
        $initiative = Initiative::factory()->create([
            'campaign_id' => $campaign,
        ]);

        self::actingAs($user)
            ->deleteJson(\sprintf(
                '/api/campaigns/%d/initiatives',
                $campaign->id,
            ))
            ->assertForbidden();
        self::assertModelExists($initiative);
    }

    /**
     * Test deleting all initiatives for a campaign.
     */
    public function testTruncateInitiatives(): void
    {
        /** @var User */
        $gm = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create(['gm' => $gm]);

        /** @var Initiative */
        $initiative = Initiative::factory()->create([
            'campaign_id' => $campaign,
        ]);

        self::actingAs($gm)
            ->deleteJson(\sprintf(
                '/api/campaigns/%d/initiatives',
                $campaign->id,
            ))
            ->assertNoContent();
        self::assertModelMissing($initiative);
    }
}
