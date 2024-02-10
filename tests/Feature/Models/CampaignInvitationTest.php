<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Events\CampaignInvitationCreated;
use App\Events\CampaignInvitationUpdated;
use App\Models\Campaign;
use App\Models\CampaignInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Tests for the CampaignInvitation model.
 * @group campaigns
 * @medium
 */
final class CampaignInvitationTest extends TestCase
{
    use WithFaker;

    public function testCreatingInviteSendsEvent(): void
    {
        Event::fake();

        /** @var Campaign */
        $campaign = Campaign::factory()->create();

        CampaignInvitation::create([
            'campaign_id' => $campaign->id,
            'email' => $this->faker->safeEmail,
            'invited_by' => User::factory()->create()->id,
            'name' => $this->faker->name,
            'status' => CampaignInvitation::INVITED,
        ]);

        Event::assertDispatched(CampaignInvitationCreated::class);
    }

    public function testUpdatingInviteSendsEvent(): void
    {
        Event::fake();

        /** @var Campaign */
        $campaign = Campaign::factory()->create();

        $invitation = CampaignInvitation::create([
            'campaign_id' => $campaign->id,
            'email' => $this->faker->safeEmail,
            'invited_by' => User::factory()->create()->id,
            'name' => $this->faker->name,
            'status' => CampaignInvitation::INVITED,
        ]);

        $invitation->status = CampaignInvitation::RESPONDED;
        $invitation->responded_at = now()->toDateTimeString();
        $invitation->update();

        Event::assertDispatched(CampaignInvitationUpdated::class);
    }
}
