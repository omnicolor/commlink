<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\Campaign;
use App\Models\CampaignInvitation;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('user')]
#[Medium]
final class RegisteredUserControllerTest extends TestCase
{
    use WithFaker;

    public function testRegistrationScreenCanBeRendered(): void
    {
        self::get('/register')->assertOk();
    }

    public function testNewUsersCanRegister(): void
    {
        self::post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
            ->assertRedirect(RouteServiceProvider::HOME);
        self::assertAuthenticated();
    }

    #[Group('campaigns')]
    public function testNewUserWithBadCampaignInvitationHash(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create();
        $invitation = CampaignInvitation::create([
            'campaign_id' => $campaign->id,
            'email' => $this->faker->safeEmail,
            // @phpstan-ignore-next-line
            'invited_by' => $campaign->gamemaster->id,
            'name' => $this->faker->name,
        ]);

        self::post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'invitation' => $invitation->id,
            'token' => '123',
        ])
            ->assertForbidden()
            ->assertSee('The token does not appear to be valid for the invitation');
    }

    #[Group('campaigns')]
    public function testNewUserWithInvitationAlreadyRespondedTo(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create();
        $invitation = CampaignInvitation::create([
            'campaign_id' => $campaign->id,
            'email' => $this->faker->safeEmail,
            // @phpstan-ignore-next-line
            'invited_by' => $campaign->gamemaster->id,
            'name' => $this->faker->name,
            'status' => CampaignInvitation::RESPONDED,
        ]);

        self::post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'invitation' => $invitation->id,
            'token' => $invitation->hash(),
        ])
            ->assertBadRequest()
            ->assertSee('It appears you\'ve already responded to the invitation');
    }

    #[Group('campaigns')]
    public function testNewUserAcceptingInvitation(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create();
        $invitation = CampaignInvitation::create([
            'campaign_id' => $campaign->id,
            'email' => $this->faker->safeEmail,
            // @phpstan-ignore-next-line
            'invited_by' => $campaign->gamemaster->id,
            'name' => $this->faker->name,
        ]);

        self::post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'invitation' => $invitation->id,
            'token' => $invitation->hash(),
        ])
            ->assertRedirect(route('campaign.view', ['campaign' => $campaign]))
            ->assertSessionHasNoErrors();

        $invitation->refresh();
        $campaign->refresh();
        self::assertNotNull($invitation->responded_at);
        self::assertSame(CampaignInvitation::RESPONDED, $invitation->status);
        self::assertCount(1, $campaign->users);
    }
}
