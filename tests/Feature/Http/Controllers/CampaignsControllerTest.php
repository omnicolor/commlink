<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignInvitation;
use App\Models\Shadowrun5e\Character;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function array_keys;
use function count;
use function in_array;
use function json_encode;

/**
 * Tests for the campaigns controller.
 * @group campaigns
 * @medium
 */
final class CampaignsControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Test an unauthenticated request to the campaign creation form.
     * @test
     */
    public function testUnauthenticated(): void
    {
        $this->get(route('campaign.createForm'))
            ->assertRedirect('/login');
    }

    /**
     * Test loading the campaign creation form.
     * @test
     */
    public function testLoadForm(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get(route('campaign.createForm'))
            ->assertSee($user->email)
            ->assertSee('Create campaign', false);
    }

    /**
     * Test creating a new campaign.
     * @test
     */
    public function testCreateNewCampaign(): void
    {
        if (
            1 === count(config('app.systems'))
            && isset(config('app.systems')['shadowrun5e'])
        ) {
            self::markTestSkipped('Shadowrun 5E is the only available system');
        }
        // @phpstan-ignore-next-line
        $name = $this->faker->catchPhrase();

        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->postJson(
                route('campaign.create'),
                [
                    'name' => $name,
                    'system' => 'dnd5e',
                ]
            )
            ->assertRedirect('/dashboard');
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
     * @test
     */
    public function testCreateNewAvatarCampaign(): void
    {
        if (!in_array('avatar', array_keys(config('app.systems')), true)) {
            self::markTestSkipped('Avatar Legends system not enabled');
        }
        // @phpstan-ignore-next-line
        $name = $this->faker->catchPhrase();

        // @phpstan-ignore-next-line
        $description = $this->faker->bs();

        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->postJson(
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
            ->assertRedirect('/dashboard');

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
     * @test
     */
    public function testCreateNewSr5eCampaign(): void
    {
        if (!in_array('shadowrun5e', array_keys(config('app.systems')), true)) {
            self::markTestSkipped('Shadowrun 5E not enabled');
        }
        // @phpstan-ignore-next-line
        $name = $this->faker->catchPhrase();

        // @phpstan-ignore-next-line
        $description = $this->faker->bs();

        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
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
            ->assertRedirect('/dashboard');

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
     * @test
     */
    public function testCreateNewCyberpunkredCampaign(): void
    {
        if (!in_array('cyberpunkred', array_keys(config('app.systems')), true)) {
            self::markTestSkipped('Cyberpunk Red not enabled');
        }
        // @phpstan-ignore-next-line
        $name = $this->faker->catchPhrase();

        // @phpstan-ignore-next-line
        $description = $this->faker->bs();

        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->postJson(
                route('campaign.createForm'),
                [
                    'description' => $description,
                    'name' => $name,
                    'night-city-tarot' => true,
                    'system' => 'cyberpunkred',
                ]
            )
            ->assertRedirect('/dashboard');

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

    /**
     * Test trying to view a campaign without being authorized.
     * @test
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
     * @test
     */
    public function testViewCampaignNotAllowed(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create();
        $this->actingAs($user)
            ->get(route('campaign.view', $campaign))
            ->assertForbidden();
    }

    /**
     * Test trying to view a campaign as the person that registered it.
     * @test
     */
    public function testViewCampaignAsRegisterer(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create([
            'registered_by' => $user->id,
        ]);

        $this->actingAs($user)
            ->get(route('campaign.view', $campaign))
            ->assertOk();
    }

    /**
     * Test trying to view a campaign as the GM.
     * @test
     */
    public function testViewCampaignAsGm(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create([
            'gm' => $user,
        ]);

        $this->actingAs($user)
            ->get(route('campaign.view', $campaign))
            ->assertOk();
    }

    /**
     * Test loading GM screen as a non-GM.
     * @test
     */
    public function testViewGmScreenAsNonGM(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create([]);
        $this->actingAs($user)
            ->get(route('campaign.gm-screen', $campaign))
            ->assertForbidden();
    }

    /**
     * Test loading GM screen as a GM for a system that doesn't yet have it.
     * @test
     */
    public function testViewGmScreenNotSupported(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create([
            'gm' => $user,
            'system' => 'capers',
        ]);
        $this->actingAs($user)
            ->get(route('campaign.gm-screen', $campaign))
            ->assertNotFound();
    }

    /**
     * Test loading a GM screen as a GM for a supported system.
     * @test
     */
    public function testViewCyberpunkredGmScreen(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create([
            'gm' => $user,
            'system' => 'cyberpunkred',
        ]);
        $this->actingAs($user)
            ->get(route('campaign.gm-screen', $campaign))
            ->assertOk();
    }

    /**
     * Test loading a Shadowrun GM screen.
     * @test
     */
    public function testViewShadowrun5eGmScreen(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'gm' => $user,
            'system' => 'shadowrun5e',
        ]);
        $character = Character::factory()->create([
            'campaign_id' => $campaign,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        $this->actingAs($user)
            ->get(route('campaign.gm-screen', $campaign))
            ->assertOk()
            ->assertSee((string)$character, true);
        $character->delete();
    }

    public function testShowCampaignWithoutAccess(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()->create();
        self::actingAs($user)
            ->get(route('campaigns.show', $campaign))
            ->assertForbidden();
    }

    public function testShowCampaignAsPlayerWithCharacter(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
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
        /** @var Campaign */
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
                    'collection' => '/campaigns',
                    'root' => '/',
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
        /** @var Campaign */
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
        /** @var Campaign */
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
        /** @var Campaign */
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
        /** @var Campaign */
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
        /** @var Campaign */
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
        /** @var Campaign */
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

        /** @var Campaign */
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

        /** @var Campaign */
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

        /** @var Campaign */
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

        /** @var Campaign */
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
                    'email' => $invitee->email,
                    'name' => $invitee->name,
                ],
            ]);
    }
}
