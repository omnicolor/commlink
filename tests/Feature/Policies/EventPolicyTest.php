<?php

declare(strict_types=1);

namespace Tests\Feature\Policies;

use App\Models\Campaign;
use App\Models\Event;
use App\Models\User;
use App\Policies\EventPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for the Event policy.
 * @group events
 * @medium
 */
final class EventPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected EventPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new EventPolicy();
    }

    public function testViewAny(): void
    {
        $user = User::factory()->make();
        self::assertFalse($this->policy->viewAny($user));
    }

    public function testViewEventNotLoggedIn(): void
    {
        $event = Event::factory()->create();
        self::assertFalse($this->policy->view(null, $event));
    }

    /**
     * Test a user trying to view an event without anything tying them to the
     * event.
     */
    public function testViewEventNotAllowed(): void
    {
        $user = User::factory()->create();
        $event = Event::create([
            // @phpstan-ignore-next-line
            'campaign_id' => Campaign::factory()->create()->id,
            'created_by' => User::factory()->create()->id,
            'name' => 'EventPolicyTest::testViewEventNotAllowed',
            'real_start' => now(),
        ]);
        self::assertFalse($this->policy->view($user, $event));
    }

    public function testViewAsCreator(): void
    {
        $user = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create();
        $event = Event::create([
            'campaign_id' => $campaign->id,
            'created_by' => $user->id,
            'name' => 'EventPolicyTest::testViewEventAsGm',
            'real_start' => now(),
        ]);
        self::assertTrue($this->policy->view($user, $event));
    }

    public function testViewAsGm(): void
    {
        $user = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create(['gm' => $user->id]);
        $event = Event::create([
            'campaign_id' => $campaign->id,
            'created_by' => User::factory()->create()->id,
            'name' => 'EventPolicyTest::testViewEventAsGm',
            'real_start' => now(),
        ]);
        self::assertTrue($this->policy->view($user, $event));
    }

    public function testViewBannedUser(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()
            ->hasAttached($user, ['status' => 'banned'])
            ->create();
        $event = Event::create([
            'campaign_id' => $campaign->id,
            'created_by' => User::factory()->create()->id,
            'name' => 'EventPolicyTest::testViewBannedUser',
            'real_start' => now(),
        ]);
        self::assertFalse($this->policy->view($user, $event));
    }

    public function testViewAsPlayer(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()
            ->hasAttached($user, ['status' => 'accepted'])
            ->create();
        $event = Event::create([
            'campaign_id' => $campaign->id,
            'created_by' => User::factory()->create()->id,
            'name' => 'EventPolicyTest::testViewAsPlayer',
            'real_start' => now(),
        ]);
        self::assertTrue($this->policy->view($user, $event));
    }

    public function testCreateEvent(): void
    {
        $user = User::factory()->create();
        self::assertFalse($this->policy->create($user));
    }

    public function testUpdateEventAsNobody(): void
    {
        $user = User::factory()->create();
        $event = Event::create([
            // @phpstan-ignore-next-line
            'campaign_id' => Campaign::factory()->create()->id,
            'created_by' => User::factory()->create()->id,
            'name' => 'EventPolicyTest::testUpdateEventAsNobody',
            'real_start' => now(),
        ]);
        self::assertFalse($this->policy->update($user, $event));
    }

    public function testUpdateEventAsCreator(): void
    {
        $user = User::factory()->create();
        $event = Event::create([
            // @phpstan-ignore-next-line
            'campaign_id' => Campaign::factory()->create()->id,
            'created_by' => $user->id,
            'name' => 'EventPolicyTest::testUpdateEventAsCreator',
            'real_start' => now(),
        ]);
        self::assertTrue($this->policy->update($user, $event));
    }

    public function testUpdateEventAsGM(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create(['gm' => $user->id]);
        $event = Event::create([
            // @phpstan-ignore-next-line
            'campaign_id' => $campaign->id,
            'created_by' => User::factory()->create()->id,
            'name' => 'EventPolicyTest::testUpdateEventAsCreator',
            'real_start' => now(),
        ]);
        self::assertTrue($this->policy->update($user, $event));
    }

    public function testDeleteAsNobody(): void
    {
        $user = User::factory()->create();
        $event = Event::create([
            // @phpstan-ignore-next-line
            'campaign_id' => Campaign::factory()->create()->id,
            'created_by' => User::factory()->create()->id,
            'name' => 'EventPolicyTest::testDeleteEventAsNobody',
            'real_start' => now(),
        ]);
        self::assertFalse($this->policy->delete($user, $event));
    }

    public function testDeleteEventAsCreator(): void
    {
        $user = User::factory()->create();
        $event = Event::create([
            // @phpstan-ignore-next-line
            'campaign_id' => Campaign::factory()->create()->id,
            'created_by' => $user->id,
            'name' => 'EventPolicyTest::testDeleteEventAsCreator',
            'real_start' => now(),
        ]);
        self::assertTrue($this->policy->delete($user, $event));
    }

    public function testDeleteEventAsGM(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create(['gm' => $user->id]);
        $event = Event::create([
            // @phpstan-ignore-next-line
            'campaign_id' => $campaign->id,
            'created_by' => User::factory()->create()->id,
            'name' => 'EventPolicyTest::testDeleteEventAsCreator',
            'real_start' => now(),
        ]);
        self::assertTrue($this->policy->delete($user, $event));
    }

    public function testRestoreAsNobody(): void
    {
        $user = User::factory()->create();
        $event = Event::create([
            // @phpstan-ignore-next-line
            'campaign_id' => Campaign::factory()->create()->id,
            'created_by' => User::factory()->create()->id,
            'name' => 'EventPolicyTest::testRestoreAsNobody',
            'real_start' => now(),
        ]);
        self::assertFalse($this->policy->restore($user, $event));
    }

    public function testRestoreEventAsCreator(): void
    {
        $user = User::factory()->create();
        $event = Event::create([
            // @phpstan-ignore-next-line
            'campaign_id' => Campaign::factory()->create()->id,
            'created_by' => $user->id,
            'name' => 'EventPolicyTest::testRestoreEventAsCreator',
            'real_start' => now(),
        ]);
        self::assertTrue($this->policy->restore($user, $event));
    }

    public function testRestoreEventAsGM(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create(['gm' => $user->id]);
        $event = Event::create([
            // @phpstan-ignore-next-line
            'campaign_id' => $campaign->id,
            'created_by' => User::factory()->create()->id,
            'name' => 'EventPolicyTest::testRestoreEventAsCreator',
            'real_start' => now(),
        ]);
        self::assertTrue($this->policy->restore($user, $event));
    }

    public function testForceDelete(): void
    {
        $user = User::factory()->create();
        $event = Event::create([
            // @phpstan-ignore-next-line
            'campaign_id' => Campaign::factory()->create()->id,
            'created_by' => User::factory()->create()->id,
            'name' => 'EventPolicyTest::testViewEventNotAllowed',
            'real_start' => now(),
        ]);
        self::assertFalse($this->policy->forceDelete($user, $event));
    }
}
