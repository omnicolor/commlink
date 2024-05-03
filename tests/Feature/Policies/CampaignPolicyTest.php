<?php

declare(strict_types=1);

namespace Tests\Feature\Policies;

use App\Models\Campaign;
use App\Models\User;
use App\Policies\CampaignPolicy;
use Tests\TestCase;

/**
 * Tests for the Campaigns policy.
 * @group campaigns
 * @medium
 */
final class CampaignPolicyTest extends TestCase
{
    protected CampaignPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new CampaignPolicy();
    }

    /**
     * Test the viewAny method. It doesn't matter what the user is.
     * @test
     */
    public function testViewAny(): void
    {
        /** @var User */
        $user = User::factory()->make();
        self::assertFalse($this->policy->viewAny($user));
    }

    /**
     * Test trying to view a campaign if the user isn't the one that registered
     * it, or the GM, or a player.
     * @test
     */
    public function testViewNoConnection(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()->make();
        self::assertFalse($this->policy->view($user, $campaign));
    }

    /**
     * Test trying to view a campaign as the user that registered it.
     * @test
     */
    public function testViewAsRegisterer(): void
    {
        /** @var User */
        $user = User::factory()->make();
        /** @var Campaign */
        $campaign = Campaign::factory()->make([
            'registered_by' => $user,
        ]);
        self::assertTrue($this->policy->view($user, $campaign));
    }

    /**
     * Test trying to view a campaign as the GM.
     * @test
     */
    public function testViewAsGm(): void
    {
        /** @var User */
        $user = User::factory()->make();
        /** @var Campaign */
        $campaign = Campaign::factory()->make([
            'gm' => $user,
        ]);
        self::assertTrue($this->policy->view($user, $campaign));
    }

    /**
     * Test trying to view a campaign as both the registerer and GM.
     * @test
     */
    public function testViewAsGmAndRegisterer(): void
    {
        /** @var User */
        $user = User::factory()->make();
        /** @var Campaign */
        $campaign = Campaign::factory()->make([
            'registered_by' => $user,
            'gm' => $user,
        ]);
        self::assertTrue($this->policy->view($user, $campaign));
    }

    /**
     * Test trying to view a campaign as a player that has been invited.
     * @test
     */
    public function testViewAsInvitedPlayer(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()
            ->hasAttached($user, ['status' => 'invited'])
            ->create();
        self::assertTrue($this->policy->view($user, $campaign));
    }

    /**
     * Test trying to view a campaign as a player that accepted the invite.
     * @test
     */
    public function testViewAsAcceptedPlayer(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()
            ->hasAttached($user, ['status' => 'accepted'])
            ->create();
        self::assertTrue($this->policy->view($user, $campaign));
    }

    /**
     * Test trying to view a campaign as a player that has been removed from the
     * campaign.
     * @test
     */
    public function testViewAsRemovedPlayer(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()
            ->hasAttached($user, ['status' => 'removed'])
            ->create();
        self::assertFalse($this->policy->view($user, $campaign));
    }

    /**
     * Test trying to view a campaign as a player that has been banned.
     * @test
     */
    public function testViewAsBannedPlayer(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()
            ->hasAttached($user, ['status' => 'banned'])
            ->create();
        self::assertFalse($this->policy->view($user, $campaign));
    }

    /**
     * Any user can create a campaign.
     * @test
     */
    public function testCreate(): void
    {
        /** @var User */
        $user = User::factory()->make();
        self::assertTrue($this->policy->create($user));
    }

    public function testUpdateAsGm(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory([
            'gm' => $user,
            'registered_by' => $user,
        ])
            ->create();
        self::assertTrue($this->policy->update($user, $campaign));
    }

    public function testUpdate(): void
    {
        $user = User::factory()->create();
        $gm = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory([
            'gm' => $gm,
            'registered_by' => $user,
        ])
            ->hasAttached($user, ['status' => 'accepted'])
            ->create();
        self::assertFalse($this->policy->update($user, $campaign));
    }

    public function testDeleteAsUnaffiliatedUser(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()->create();
        self::assertFalse($this->policy->delete($user, $campaign));
    }

    public function testDeleteAsRegistrant(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory(['registered_by' => $user])
            ->create();
        self::assertTrue($this->policy->delete($user, $campaign));
    }

    public function testDeleteAsGm(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory(['gm' => $user])
            ->create();
        self::assertTrue($this->policy->delete($user, $campaign));
    }

    public function testDeleteAsPlayer(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()
            ->hasAttached($user, ['status' => 'accepted'])
            ->create();
        self::assertFalse($this->policy->delete($user, $campaign));
    }

    /**
     * No user can currently restore a campaign, even the one that registered it
     * and acts as both the GM and a player.
     * @test
     */
    public function testRestore(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory([
            'gm' => $user,
            'registered_by' => $user,
        ])
            ->hasAttached($user, ['status' => 'accepted'])
            ->create();
        self::assertFalse($this->policy->restore($user, $campaign));
    }

    /**
     * No user can currently force delete a campaign, even the one that
     * registered it and acts as both the GM and a player.
     * @test
     */
    public function testForceDelete(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory([
            'gm' => $user,
            'registered_by' => $user,
        ])
            ->hasAttached($user, ['status' => 'accepted'])
            ->create();
        self::assertFalse($this->policy->forceDelete($user, $campaign));
    }

    /**
     * Test whether a normal user can GM a campaign.
     * @test
     */
    public function testNonGmTryingToGm(): void
    {
        /** @var User */
        $user = User::factory()->make();

        /** @var Campaign */
        $campaign = Campaign::factory()->make();
        self::assertFalse($this->policy->gm($user, $campaign));
    }

    /**
     * Test a GM trying to GM a campaign.
     * @test
     */
    public function testGmTryingToGm(): void
    {
        /** @var User */
        $user = User::factory()->make();

        /** @var Campaign */
        $campaign = Campaign::factory()->make([
            'gm' => $user->id,
        ]);
        self::assertTrue($this->policy->gm($user, $campaign));
    }

    public function testInviteAsGm(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()->make([
            'gm' => $user->id,
        ]);
        self::assertTrue($this->policy->invite($user, $campaign));
    }

    public function testInviteAsRegistrant(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()->make([
            'registered_by' => $user,
        ]);
        self::assertTrue($this->policy->invite($user, $campaign));
    }

    public function testInviteAsPlayer(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()
            ->hasAttached($user, ['status' => 'accepted'])
            ->create();
        self::assertFalse($this->policy->invite($user, $campaign));
    }
}
