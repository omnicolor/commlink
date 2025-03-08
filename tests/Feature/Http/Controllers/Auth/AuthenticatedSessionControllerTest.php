<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\Campaign;
use App\Models\CampaignInvitation;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

/**
 * If tests in this file fail with `LogicException: An email must have a "From"
 * or a "Sender" header.` add a value for MAIL_FROM_ADDRESS in your
 * .env.testing file.
 */
#[Group('user')]
#[Medium]
final class AuthenticatedSessionControllerTest extends TestCase
{
    use WithFaker;

    public function testLoginScreenCanBeRendered(): void
    {
        self::get('/login')->assertOk();
    }

    public function testUsersCanAuthenticateUsingTheLoginScreen(): void
    {
        $user = User::factory()->create();

        self::post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertRedirect(RouteServiceProvider::HOME);

        self::assertAuthenticated();
    }

    public function testUsersCanNotAuthenticateWithInvalidPassword(): void
    {
        $user = User::factory()->create();

        self::post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        self::assertGuest();
    }

    #[Group('campaigns')]
    public function testUserLoginWithInvitationBadHash(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create();
        $invitation = CampaignInvitation::create([
            'campaign_id' => $campaign->id,
            'email' => $this->faker->safeEmail,
            'invited_by' => $campaign->gamemaster?->id,
            'name' => $this->faker->name,
        ]);

        self::post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'invitation' => $invitation->id,
            'token' => '123',
        ])
            ->assertForbidden()
            ->assertSee('The token does not appear to be valid for the invitation');
    }

    #[Group('campaigns')]
    public function testUserLoginWithInvitationAlreadyResponded(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create();
        $invitation = CampaignInvitation::create([
            'campaign_id' => $campaign->id,
            'email' => $this->faker->safeEmail,
            'invited_by' => $campaign->gamemaster?->id,
            'name' => $this->faker->name,
            'status' => CampaignInvitation::RESPONDED,
        ]);

        self::post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'invitation' => $invitation->id,
            'token' => $invitation->hash(),
        ])
            ->assertBadRequest()
            ->assertSee('It appears you\'ve already responded to the invitation');
    }

    #[Group('campaigns')]
    public function testUserLoginWithInvitationAsGM(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create(['gm' => $user->id]);
        $invitation = CampaignInvitation::create([
            'campaign_id' => $campaign->id,
            'email' => $this->faker->safeEmail,
            'invited_by' => $user->id,
            'name' => $this->faker->name,
        ]);

        self::post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'invitation' => $invitation->id,
            'token' => $invitation->hash(),
        ])
            ->assertConflict()
            ->assertSee('You can\'t join a game when you\'re the GM');
    }

    #[Group('campaigns')]
    public function testUserAcceptingInvitation(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create();
        $invitation = CampaignInvitation::create([
            'campaign_id' => $campaign->id,
            'email' => $this->faker->safeEmail,
            'invited_by' => $campaign->gamemaster?->id,
            'name' => $this->faker->name,
        ]);

        self::post('/login', [
            'email' => $user->email,
            'password' => 'password',
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
