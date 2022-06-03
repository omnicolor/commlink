<?php

declare(strict_types=1);

namespace Tests\Feature\Policies;

use App\Models\Campaign;
use App\Models\User;
use App\Policies\CampaignPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Tests for the Campaigns policy.
 * @group campaigns
 * @medium
 */
final class CampaignPolicyTest extends \Tests\TestCase
{
    use RefreshDatabase;

    protected CampaignPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new CampaignPolicy();
    }

    /**
     * Test the viewAny method. It doesn't matter what the user is.
     * @small
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
     * @small
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
     * Test trying to view a campaign as a player.
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
     * @small
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
}
