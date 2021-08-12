<?php

declare(strict_types=1);

namespace Tests\Feature\Policies;

use App\Models\Campaign;
use App\Models\User;
use App\Policies\CampaignPolicy;

/**
 * Tests for the Campaigns policy.
 * @group campaigns
 * @small
 */
final class CampaignPolicyTest extends \Tests\TestCase
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
        $user = User::factory()->make();
        /** @var Campaign */
        $campaign = Campaign::factory()->make();
        self::assertFalse($this->policy->view($user, $campaign));
    }

    /**
     * Test trying to view a campaign as the user that registered it.
     * @medium
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
     * Test trying to view a campaign as a player.
     * @medium
     * @test
     */
    public function testViewAsPlayer(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()
            ->hasAttached($user, ['status' => 'accepted'])
            ->create();
        self::assertTrue($this->policy->view($user, $campaign));
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

    /**
     * No user can currently update a campaign, even the one that registered it
     * and acts as both the GM and a player.
     * @medium
     * @test
     */
    public function testUpdate(): void
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
        self::assertFalse($this->policy->update($user, $campaign));
    }

    /**
     * No user can currently delete a campaign, even the one that registered it
     * and acts as both the GM and a player.
     * @medium
     * @test
     */
    public function testDelete(): void
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
        self::assertFalse($this->policy->delete($user, $campaign));
    }

    /**
     * No user can currently restore a campaign, even the one that registered it
     * and acts as both the GM and a player.
     * @medium
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
     * @medium
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
}
