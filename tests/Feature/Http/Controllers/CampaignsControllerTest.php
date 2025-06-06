<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Enums\CampaignInvitationStatus;
use App\Models\Campaign;
use App\Models\CampaignInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Modules\Shadowrun5e\Models\Character;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function array_keys;
use function count;
use function in_array;
use function json_encode;

#[Group('campaigns')]
#[Medium]
final class CampaignsControllerTest extends TestCase
{
    use WithFaker;

    /**
     * Test loading the campaign creation form.
     */
    public function testLoadForm(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->get(route('campaign.createForm'))
            ->assertSee($user->email->address)
            ->assertSee('Create campaign', false);
    }

    /**
     * Test creating a new campaign.
     */
    public function testCreateNewDnd5eCampaign(): void
    {
        if (
            1 === count(config('commlink.systems'))
            && isset(config('commlink.systems')['shadowrun5e'])
        ) {
            self::markTestSkipped('Shadowrun 5E is the only available system');
        }
        $name = $this->faker->catchPhrase();
        $user = User::factory()->create();
        self::actingAs($user)
            ->postJson(
                route('campaign.create'),
                [
                    'name' => $name,
                    'system' => 'dnd5e',
                ]
            )
            ->assertRedirect(
                route('campaign.view', Campaign::latest()->firstOrFail())
            );
        $this->assertDatabaseHas(
            'campaigns',
            [
                'description' => null,
                'gm' => null,
                'name' => $name,
                'options' => null,
                'registered_by' => $user->id,
                'system' => 'dnd5e',
            ]
        );
    }

    /**
     * Test creating a new Avatar Legends campaign with options.
     */
    public function testCreateNewAvatarCampaign(): void
    {
        if (!in_array('avatar', array_keys(config('commlink.systems')), true)) {
            self::markTestSkipped('Avatar Legends system not enabled');
        }
        $name = $this->faker->catchPhrase();

        $description = $this->faker->bs();

        $user = User::factory()->create();
        self::actingAs($user)->postJson(
            route('campaign.createForm'),
            [
                    'description' => $description,
                    'name' => $name,
                    'system' => 'avatar',
                    'avatar-era' => 'aang',
                    'avatar-scope' => 'Scope of the campaign',
                    'avatar-focus' => 'defeat',
                    'avatar-focus-details' => 'Details about the focus',
                    'avatar-focus-defeat-object' => 'the big bad guy',
                ]
        )
            ->assertRedirect(
                route('campaign.view', Campaign::latest()->firstOrFail())
            );

        $expectedOptions = json_encode([
            'era' => 'aang',
            'scope' => 'Scope of the campaign',
            'focus' => 'defeat',
            'focusDetails' => 'Details about the focus',
            'focusObject' => 'the big bad guy',
        ]);
        $this->assertDatabaseHas(
            'campaigns',
            [
                'description' => $description,
                'gm' => null,
                'name' => $name,
                'options' => $expectedOptions,
                'registered_by' => $user->id,
                'system' => 'avatar',
            ]
        );
    }

    /**
     * Test creating a new Shadowrun 5E campaign with options.
     */
    public function testCreateNewSr5eCampaign(): void
    {
        if (!in_array('shadowrun5e', array_keys(config('commlink.systems')), true)) {
            self::markTestSkipped('Shadowrun 5E not enabled');
        }
        $name = $this->faker->catchPhrase();

        $description = $this->faker->bs();

        $user = User::factory()->create();
        self::actingAs($user)
            ->postJson(
                route('campaign.createForm'),
                [
                    'description' => $description,
                    'name' => $name,
                    'sr5e-creation' => [
                        'priority',
                        'sum-to-ten',
                    ],
                    'sr5e-gameplay' => 'established',
                    'sr5e-rules' => [
                        'core',
                        'run-faster',
                    ],
                    'sr5e-start-date' => '2080-04-01',
                    'system' => 'shadowrun5e',
                ]
            )
            ->assertRedirect(
                route('campaign.view', Campaign::latest()->firstOrFail())
            );

        $expectedOptions = json_encode([
            'creation' => ['priority', 'sum-to-ten'],
            'gameplay' => 'established',
            'rulesets' => ['core', 'run-faster'],
            'startDate' => '2080-04-01',
        ]);
        $this->assertDatabaseHas(
            'campaigns',
            [
                'description' => $description,
                'gm' => null,
                'name' => $name,
                'options' => $expectedOptions,
                'registered_by' => $user->id,
                'system' => 'shadowrun5e',
            ]
        );
    }

    /**
     * Test creating a new Cyberpunk Red campaign with options.
     */
    public function testCreateNewCyberpunkredCampaign(): void
    {
        if (!in_array('cyberpunkred', array_keys(config('commlink.systems')), true)) {
            self::markTestSkipped('Cyberpunk Red not enabled');
        }
        $name = $this->faker->catchPhrase();

        $description = $this->faker->bs();

        $user = User::factory()->create();
        self::actingAs($user)
            ->postJson(
                route('campaign.createForm'),
                [
                    'description' => $description,
                    'name' => $name,
                    'night-city-tarot' => true,
                    'system' => 'cyberpunkred',
                ]
            )
            ->assertRedirect(
                route('campaign.view', Campaign::latest()->firstOrFail())
            );

        $expectedOptions = json_encode([
            'nightCityTarot' => true,
        ]);
        $this->assertDatabaseHas(
            'campaigns',
            [
                'description' => $description,
                'gm' => null,
                'name' => $name,
                'options' => $expectedOptions,
                'registered_by' => $user->id,
                'system' => 'cyberpunkred',
            ]
        );
    }

    public function testCreateNewSubversionCampaign(): void
    {
        if (!in_array('subversion', array_keys(config('commlink.systems')), true)) {
            self::markTestSkipped('Subversion not enabled');
        }
        $name = $this->faker->catchPhrase();
        $description = $this->faker->bs();
        $communityDescription = $this->faker->bs();
        $user = User::factory()->create();
        self::actingAs($user)
            ->postJson(
                route('campaign.createForm'),
                [
                    'description' => $description,
                    'name' => $name,
                    'subversion-community-description' => $communityDescription,
                    'subversion-community-type' => 'neighborhood',
                    'system' => 'subversion',
                ]
            )
            ->assertRedirect(
                route('campaign.view', Campaign::latest()->firstOrFail())
            );

        $expectedOptions = json_encode([
            'community-description' => $communityDescription,
            'community-type' => 'neighborhood',
        ]);
        $this->assertDatabaseHas(
            'campaigns',
            [
                'description' => $description,
                'gm' => null,
                'name' => $name,
                'options' => $expectedOptions,
                'registered_by' => $user->id,
                'system' => 'subversion',
            ]
        );
    }

    /**
     * Test trying to view a campaign without being authorized.
     */
    public function testViewCampaignUnauthenticated(): void
    {
        $campaign = Campaign::factory()->create();
        $this->get(route('campaign.view', $campaign))
            ->assertRedirect('/login');
    }

    /**
     * Test trying to view a campaign without being a player, the GM, or the
     * person that registered the campaign.
     */
    public function testViewCampaignNotAllowed(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create();
        self::actingAs($user)
            ->get(route('campaign.view', $campaign))
            ->assertForbidden();
    }

    /**
     * Test trying to view a campaign as the person that registered it.
     */
    public function testViewCampaignAsRegisterer(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create([
            'registered_by' => $user->id,
        ]);

        self::actingAs($user)
            ->get(route('campaign.view', $campaign))
            ->assertOk();
    }

    /**
     * Test trying to view a campaign as the GM.
     */
    public function testViewCampaignAsGm(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create([
            'gm' => $user,
        ]);

        self::actingAs($user)
            ->get(route('campaign.view', $campaign))
            ->assertOk();
    }

    /**
     * Test loading GM screen as a non-GM.
     */
    public function testViewGmScreenAsNonGM(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create([]);
        self::actingAs($user)
            ->get(route('campaign.gm-screen', $campaign))
            ->assertForbidden();
    }

    /**
     * Test loading GM screen as a GM for a system that doesn't yet have it.
     */
    public function testViewGmScreenNotSupported(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create([
            'gm' => $user,
            'system' => 'capers',
        ]);
        self::actingAs($user)
            ->get(route('campaign.gm-screen', $campaign))
            ->assertNotFound();
    }

    /**
     * Test loading a GM screen as a GM for a supported system.
     */
    public function testViewCyberpunkredGmScreen(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create([
            'gm' => $user,
            'system' => 'cyberpunkred',
        ]);
        self::actingAs($user)
            ->get(route('campaign.gm-screen', $campaign))
            ->assertOk();
    }

    /**
     * Test loading a Shadowrun GM screen.
     */
    public function testViewShadowrun5eGmScreen(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create([
            'gm' => $user,
            'system' => 'shadowrun5e',
        ]);
        // Handle flaky test from previous failures causing a non-Shadowrun 5E
        // character to show up for this campaign.
        Character::where('campaign_id', $campaign->id)
            ->delete();
        $character = Character::factory()->create([
            'campaign_id' => $campaign,
            'system' => 'shadowrun5e',
        ]);

        self::actingAs($user)
            ->get(route('campaign.gm-screen', $campaign))
            ->assertOk()
            ->assertSee((string)$character, true);
        $character->delete();
    }

    public function testShowCampaignWithoutAccess(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create();
        self::actingAs($user)
            ->get(route('campaigns.show', $campaign))
            ->assertForbidden();
    }

    public function testShowCampaignAsPlayerWithCharacter(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()
            ->hasAttached($user, ['status' => 'accepted'])
            ->create(['system' => 'expanse']);
        $character = Character::factory()
            ->create([
                'campaign_id' => $campaign->id,
                'owner' => $user->email,
                'system' => $campaign->system,
            ]);
        self::actingAs($user)
            ->get(route('campaigns.show', $campaign))
            ->assertOk()
            ->assertJsonCount(1, 'data.characters');
        $character->delete();
    }

    public function testShowCampaignAsGm(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create(['gm' => $user->id]);
        self::actingAs($user)
            ->get(route('campaigns.show', $campaign))
            ->assertOk();
    }

    public function testListCampaignsWithoutAny(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->get(route('campaigns.index'))
            ->assertOk()
            ->assertJson([
                'data' => [],
                'links' => [
                    'self' => route('campaigns.index'),
                ],
            ]);
    }

    public function testListCampaigns(): void
    {
        $user = User::factory()->create();
        Campaign::factory()->create(['gm' => $user->id]);
        Campaign::factory()->create(['registered_by' => $user->id]);
        Campaign::factory()
            ->hasAttached($user, ['status' => 'invited'])
            ->create();
        Campaign::factory()
            ->hasAttached($user, ['status' => 'banned'])
            ->create();
        self::actingAs($user)
            ->get(route('campaigns.index'))
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function testDeleteAsGm(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create(['gm' => $user->id]);
        self::actingAs($user)
            ->delete(route('campaigns.destroy', $campaign))
            ->assertNoContent();

        self::actingAs($user)
            ->delete(route('campaigns.destroy', $campaign))
            ->assertNotFound();
    }

    public function testRespondWithoutBeingInvited(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create();

        self::actingAs($user)
            ->post(
                route('campaign.respond', $campaign),
                ['response' => 'accepted']
            )
            ->assertForbidden();
    }

    public function testRespondAccepted(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()
            ->hasAttached($user, ['status' => 'invited'])
            ->create();
        self::actingAs($user)
            ->post(
                route('campaign.respond', $campaign),
                ['response' => 'accepted']
            )
            ->assertRedirectToRoute('campaign.view', $campaign);

        /** @var User */
        $player = $campaign->users->find($user->id);
        self::assertSame('accepted', $player->pivot->status);
    }

    public function testRespondDeclined(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()
            ->hasAttached($user, ['status' => 'invited'])
            ->create();
        self::actingAs($user)
            ->post(
                route('campaign.respond', $campaign),
                ['response' => 'removed']
            )
            ->assertRedirectToRoute('dashboard');

        /** @var User */
        $player = $campaign->users->find($user->id);
        self::assertSame('removed', $player->pivot->status);
    }

    public function testInviteAgain(): void
    {
        $gm = User::factory()->create();
        $inviteeEmail = $this->faker->safeEmail;
        $campaign = Campaign::factory()->create(['gm' => $gm->id]);
        CampaignInvitation::create([
            'campaign_id' => $campaign->id,
            'email' => $inviteeEmail,
            'invited_by' => $gm->id,
            'name' => $this->faker->name,
        ]);

        self::actingAs($gm)
            ->post(
                route('campaign.invite', $campaign),
                [
                    'email' => $inviteeEmail,
                    'name' => $this->faker->name,
                ]
            )
            ->assertConflict()
            ->assertSee('You have already invted that user');
    }

    public function testInviteNewUser(): void
    {
        $gm = User::factory()->create();
        $campaign = Campaign::factory()->create(['gm' => $gm->id]);
        $inviteeEmail = $this->faker->unique()->safeEmail;
        $name = $this->faker->name;

        self::actingAs($gm)
            ->post(
                route('campaign.invite', $campaign),
                [
                    'email' => $inviteeEmail,
                    'name' => $name,
                ]
            )
            ->assertCreated()
            ->assertJsonFragment([
                'meta' => ['status' => 'new'],
            ])
            ->assertJsonFragment([
                'campaign' => [
                    'id' => $campaign->id,
                    'links' => [
                        'self' => route('campaign.view', $campaign),
                    ],
                    'name' => $campaign->name,
                    'system' => $campaign->system,
                ],
                'invited_by' => [
                    'id' => $gm->id,
                    'name' => $gm->name,
                ],
                'invitee' => [
                    'email' => $inviteeEmail,
                    'name' => $name,
                ],
            ]);
    }

    public function testInviteExistingAlreadyInvited(): void
    {
        $gm = User::factory()->create();
        $invitee = User::factory()->create();

        $campaign = Campaign::factory()
            ->hasAttached($invitee, ['status' => 'invited'])
            ->create(['gm' => $gm->id]);

        self::actingAs($gm)
            ->post(
                route('campaign.invite', $campaign),
                [
                    'email' => $invitee->email,
                    'name' => $this->faker->name,
                ]
            )
            ->assertConflict()
            ->assertSee('That user has already been invited');
    }

    public function testInviteExistingAlreadyPlaying(): void
    {
        $gm = User::factory()->create();
        $invitee = User::factory()->create();

        $campaign = Campaign::factory()
            ->hasAttached($invitee, ['status' => 'accepted'])
            ->create(['gm' => $gm->id]);

        self::actingAs($gm)
            ->post(
                route('campaign.invite', $campaign),
                [
                    'email' => $invitee->email,
                    'name' => $this->faker->name,
                ]
            )
            ->assertConflict()
            ->assertSee('That user has already joined the campaign');
    }

    public function testInviteGm(): void
    {
        $gm = User::factory()->create();

        $campaign = Campaign::factory()->create(['gm' => $gm->id]);

        self::actingAs($gm)
            ->post(
                route('campaign.invite', $campaign),
                [
                    'email' => $gm->email,
                    'name' => $this->faker->name,
                ]
            )
            ->assertBadRequest()
            ->assertSee('You can\'t invite the GM to play');
    }

    public function testInviteExistingUser(): void
    {
        $gm = User::factory()->create();
        $invitee = User::factory()->create();

        $campaign = Campaign::factory()->create(['gm' => $gm->id]);

        self::actingAs($gm)
            ->post(
                route('campaign.invite', $campaign),
                [
                    'email' => $invitee->email,
                    'name' => $this->faker->name,
                ]
            )
            ->assertCreated()
            ->assertJsonFragment([
                'meta' => [
                    'status' => 'existing',
                    'user' => [
                        'id' => $invitee->id,
                        'name' => $invitee->name,
                    ],
                ],
            ])
            ->assertJsonFragment([
                'campaign' => [
                    'id' => $campaign->id,
                    'links' => [
                        'self' => route('campaign.view', $campaign),
                    ],
                    'name' => $campaign->name,
                    'system' => $campaign->system,
                ],
                'invited_by' => [
                    'id' => $gm->id,
                    'name' => $gm->name,
                ],
                'invitee' => [
                    'email' => $invitee->email->address,
                    'name' => $invitee->name,
                ],
            ]);
    }

    public function testUserAcceptingWithInvalidToken(): void
    {
        $campaign = Campaign::factory()->create();
        $invitation = CampaignInvitation::create([
            'campaign_id' => $campaign->id,
            'email' => $this->faker->safeEmail,
            'invited_by' => $campaign->gamemaster?->id,
            'name' => $this->faker->name,
        ]);

        self::get(route(
            'campaign.invitation-accept',
            [
                'campaign' => $campaign,
                'invitation' => $invitation,
                'token' => '123',
            ]
        ))
            ->assertForbidden()
            ->assertSee('The token does not appear to be valid for the invitation');
    }

    public function testUserAcceptingWithAlreadyResponded(): void
    {
        $campaign = Campaign::factory()->create();
        $invitation = CampaignInvitation::create([
            'campaign_id' => $campaign->id,
            'email' => $this->faker->safeEmail,
            'invited_by' => $campaign->gamemaster?->id,
            'name' => $this->faker->name,
            'status' => CampaignInvitationStatus::Responded,
        ]);

        self::get(route(
            'campaign.invitation-accept',
            [
                'campaign' => $campaign,
                'invitation' => $invitation,
                'token' => $invitation->hash(),
            ]
        ))
            ->assertBadRequest()
            ->assertSeeText('It appears you\'ve already responded to the invitation');
    }

    public function testUserAccepting(): void
    {
        $campaign = Campaign::factory()->create();
        $invitation = CampaignInvitation::create([
            'campaign_id' => $campaign->id,
            'email' => $this->faker->safeEmail,
            'invited_by' => $campaign->gamemaster?->id,
            'name' => $this->faker->name,
        ]);

        self::get(route(
            'campaign.invitation-accept',
            [
                'campaign' => $campaign,
                'invitation' => $invitation,
                'token' => $invitation->hash(),
            ]
        ))
            ->assertOk()
            ->assertViewIs('campaign.Invitation.accept');
    }

    public function testUserChangingEmailWithInvalidToken(): void
    {
        $campaign = Campaign::factory()->create();
        $invitation = CampaignInvitation::create([
            'campaign_id' => $campaign->id,
            'email' => $this->faker->safeEmail,
            'invited_by' => $campaign->gamemaster?->id,
            'name' => $this->faker->name,
        ]);

        self::get(route(
            'campaign.invitation-change',
            [
                'campaign' => $campaign,
                'invitation' => $invitation,
                'token' => '123',
            ]
        ))
            ->assertForbidden()
            ->assertSee('The token does not appear to be valid for the invitation');
    }

    public function testUserChangingEmailWithAlreadyResponded(): void
    {
        $campaign = Campaign::factory()->create();
        $invitation = CampaignInvitation::create([
            'campaign_id' => $campaign->id,
            'email' => $this->faker->safeEmail,
            'invited_by' => $campaign->gamemaster?->id,
            'name' => $this->faker->name,
            'status' => CampaignInvitationStatus::Responded,
        ]);

        self::get(route(
            'campaign.invitation-change',
            [
                'campaign' => $campaign,
                'invitation' => $invitation,
                'token' => $invitation->hash(),
            ]
        ))
            ->assertBadRequest()
            ->assertSeeText('It appears you\'ve already responded to the invitation');
    }

    public function testUserChangingEmail(): void
    {
        $campaign = Campaign::factory()->create();
        $invitation = CampaignInvitation::create([
            'campaign_id' => $campaign->id,
            'email' => $this->faker->safeEmail,
            'invited_by' => $campaign->gamemaster?->id,
            'name' => $this->faker->name,
        ]);

        self::get(route(
            'campaign.invitation-change',
            [
                'campaign' => $campaign,
                'invitation' => $invitation,
                'token' => $invitation->hash(),
            ]
        ))
            ->assertOk()
            ->assertViewIs('campaign.Invitation.change-email');
    }

    public function testDeclineInvitationBadHash(): void
    {
        $campaign = Campaign::factory()->create();
        $invitation = CampaignInvitation::create([
            'campaign_id' => $campaign->id,
            'email' => $this->faker->safeEmail,
            'invited_by' => $campaign->gamemaster?->id,
            'name' => $this->faker->name,
        ]);

        self::get(route(
            'campaign.invitation-decline',
            [
                'campaign' => $campaign,
                'invitation' => $invitation,
                'token' => '123',
            ]
        ))
            ->assertForbidden()
            ->assertSee('The token does not appear to be valid for the invitation');
    }

    public function testDeclineInvitationAlreadyResponded(): void
    {
        $campaign = Campaign::factory()->create();
        $invitation = CampaignInvitation::create([
            'campaign_id' => $campaign->id,
            'email' => $this->faker->safeEmail,
            'invited_by' => $campaign->gamemaster?->id,
            'name' => $this->faker->name,
            'status' => CampaignInvitationStatus::Responded,
        ]);

        self::get(route(
            'campaign.invitation-decline',
            [
                'campaign' => $campaign,
                'invitation' => $invitation,
                'token' => $invitation->hash(),
            ]
        ))
            ->assertBadRequest()
            ->assertSeeText('It appears you\'ve already responded to the invitation');
    }

    public function testDeclineInvitation(): void
    {
        $campaign = Campaign::factory()->create();
        $invitation = CampaignInvitation::create([
            'campaign_id' => $campaign->id,
            'email' => $this->faker->safeEmail,
            'invited_by' => $campaign->gamemaster?->id,
            'name' => $this->faker->name,
        ]);

        self::get(route(
            'campaign.invitation-decline',
            [
                'campaign' => $campaign,
                'invitation' => $invitation,
                'token' => $invitation->hash(),
            ]
        ))
            ->assertOk()
            ->assertViewIs('campaign.Invitation.decline');

        $invitation->refresh();
        self::assertNotNull($invitation->responded_at);
        self::assertSame(CampaignInvitationStatus::Responded, $invitation->status);
    }

    public function testSpamInvitationBadHash(): void
    {
        $campaign = Campaign::factory()->create();
        $invitation = CampaignInvitation::create([
            'campaign_id' => $campaign->id,
            'email' => $this->faker->safeEmail,
            'invited_by' => $campaign->gamemaster?->id,
            'name' => $this->faker->name,
        ]);

        self::get(route(
            'campaign.invitation-spam',
            [
                'campaign' => $campaign,
                'invitation' => $invitation,
                'token' => '123',
            ]
        ))
            ->assertForbidden()
            ->assertSee('The token does not appear to be valid for the invitation');
    }

    public function testSpamInvitationAlreadyResponded(): void
    {
        $campaign = Campaign::factory()->create();
        $invitation = CampaignInvitation::create([
            'campaign_id' => $campaign->id,
            'email' => $this->faker->safeEmail,
            'invited_by' => $campaign->gamemaster?->id,
            'name' => $this->faker->name,
            'status' => CampaignInvitationStatus::Responded,
        ]);

        self::get(route(
            'campaign.invitation-spam',
            [
                'campaign' => $campaign,
                'invitation' => $invitation,
                'token' => $invitation->hash(),
            ]
        ))
            ->assertBadRequest()
            ->assertSeeText('It appears you\'ve already responded to the invitation');
    }

    public function testSpamInvitation(): void
    {
        $campaign = Campaign::factory()->create();
        $invitation = CampaignInvitation::create([
            'campaign_id' => $campaign->id,
            'email' => $this->faker->safeEmail,
            'invited_by' => $campaign->gamemaster?->id,
            'name' => $this->faker->name,
        ]);

        self::get(route(
            'campaign.invitation-spam',
            [
                'campaign' => $campaign,
                'invitation' => $invitation,
                'token' => $invitation->hash(),
            ]
        ))
            ->assertOk()
            ->assertViewIs('campaign.Invitation.spam');

        $invitation->refresh();
        self::assertNotNull($invitation->responded_at);
        self::assertSame(CampaignInvitationStatus::Spam, $invitation->status);
    }

    public function testPatchNotGm(): void
    {
        $campaign = Campaign::factory()->create([
            'options' => ['currentDate' => '2024-02-27'],
        ]);
        self::actingAs(User::factory()->create())
            ->patchJson(route('campaign.patch', $campaign), [])
            ->assertForbidden();
    }

    public function testPatchWithInvalidContentType(): void
    {
        $gm = User::factory()->create();
        $campaign = Campaign::factory()->create([
            'gm' => $gm->id,
            'options' => ['currentDate' => '2024-02-27'],
        ]);
        self::actingAs($gm)
            ->withHeaders(['Content-Type' => 'text/xml'])
            ->patch(
                route('campaign.patch', $campaign),
                ['<xml></xml>']
            )
            ->assertUnsupportedMediaType();
    }

    public function testDataPatchInvalid(): void
    {
        $gm = User::factory()->create();
        $campaign = Campaign::factory()->create([
            'gm' => $gm->id,
            'options' => ['currentDate' => '2024-02-27'],
        ]);

        self::actingAs($gm)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->patchJson(
                route('campaign.patch', $campaign),
                ['currentDate' => 'not a valid date']
            )
            ->assertUnprocessable();
    }

    public function testDataPatchRemoveDate(): void
    {
        $gm = User::factory()->create();
        $campaign = Campaign::factory()->create([
            'gm' => $gm->id,
            'options' => ['currentDate' => '2024-02-27'],
        ]);

        self::actingAs($gm)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->patchJson(
                route('campaign.patch', $campaign),
                ['currentDate' => null]
            )
            ->assertAccepted();

        $campaign->refresh();
        self::assertNull($campaign->options['currentDate']);
    }

    public function testDataPatchUpdateDate(): void
    {
        $gm = User::factory()->create();
        $campaign = Campaign::factory()->create([
            'gm' => $gm->id,
            'options' => ['currentDate' => '2024-02-27'],
        ]);

        self::actingAs($gm)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->patchJson(
                route('campaign.patch', $campaign),
                ['currentDate' => '2024-02-29']
            )
            ->assertAccepted();

        $campaign->refresh();
        self::assertSame('2024-02-29', $campaign->options['currentDate']);
    }

    public function testJsonPatchInvalidOperationException(): void
    {
        $gm = User::factory()->create();
        $campaign = Campaign::factory()->create([
            'gm' => $gm->id,
            'options' => ['currentDate' => '2024-02-27'],
        ]);
        self::actingAs($gm)
            ->call(
                method: Request::METHOD_PATCH,
                uri: route('campaign.patch', $campaign),
                server: ['CONTENT_TYPE' => 'application/json-patch+json'],
                content: 'sdfd: {',
            )
            ->assertSee('Unable to extract patch operations')
            ->assertBadRequest();
    }

    public function testJsonPatchTypeError(): void
    {
        $gm = User::factory()->create();
        $campaign = Campaign::factory()->create([
            'gm' => $gm->id,
            'options' => ['currentDate' => '2024-02-27'],
        ]);
        self::actingAs($gm)
            ->call(
                method: Request::METHOD_PATCH,
                uri: route('campaign.patch', $campaign),
                server: ['CONTENT_TYPE' => 'application/json-patch+json'],
                content: (string)json_encode('[sdfd: {'),
            )
            ->assertBadRequest();
    }

    public function testJsonPatchInvalidPointer(): void
    {
        $gm = User::factory()->create();
        $campaign = Campaign::factory()->create([
            'gm' => $gm->id,
            'options' => ['currentDate' => '2024-02-27'],
        ]);

        self::actingAs($gm)
            ->call(
                method: Request::METHOD_PATCH,
                uri: route('campaign.patch', $campaign),
                server: ['CONTENT_TYPE' => 'application/json-patch+json'],
                content: (string)json_encode(
                    [['op' => 'remove', 'path' => 'foo']]
                ),
            )
            ->assertSee('Valid pointer values are:')
            ->assertBadRequest();
    }

    public function testJsonPatchUpdateWithDate(): void
    {
        $gm = User::factory()->create();
        $campaign = Campaign::factory()->create([
            'gm' => $gm->id,
            'options' => ['currentDate' => '2024-02-27'],
        ]);

        self::actingAs($gm)
            ->call(
                method: Request::METHOD_PATCH,
                uri: route('campaign.patch', $campaign),
                server: ['CONTENT_TYPE' => 'application/json-patch+json'],
                content: (string)json_encode(
                    [[
                        'op' => 'replace',
                        'path' => '/options/currentDate',
                        'value' => '2024-02-29',
                    ]],
                ),
            )
            ->assertAccepted()
            ->assertSee('Thursday, February 29th 2024');

        $campaign->refresh();
        self::assertSame('2024-02-29', $campaign->options['currentDate']);
    }

    public function testJsonPatchUpdateWithoutDate(): void
    {
        $gm = User::factory()->create();
        $campaign = Campaign::factory()->create([
            'gm' => $gm->id,
            'options' => ['currentDate' => '2024-02-27'],
        ]);

        self::actingAs($gm)
            ->call(
                method: Request::METHOD_PATCH,
                uri: route('campaign.patch', $campaign),
                server: ['CONTENT_TYPE' => 'application/json-patch+json'],
                content: (string)json_encode(
                    [[
                        'op' => 'remove',
                        'path' => '/options/currentDate',
                    ]],
                ),
            )
            ->assertAccepted()
            ->assertSee('no date set');

        $campaign->refresh();
        self::assertArrayNotHasKey('currentDate', $campaign->options);
    }
}
