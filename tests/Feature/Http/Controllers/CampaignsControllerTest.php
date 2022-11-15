<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\Campaign;
use App\Models\Shadowrun5e\Character;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * Tests for the campaigns controller.
 * @group campaigns
 * @group controllers
 * @medium
 */
final class CampaignsControllerTest extends \Tests\TestCase
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
            1 === \count(config('app.systems'))
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
     * Test creating a new Shadowrun 5E campaign with options.
     * @test
     */
    public function testCreateNewSr5eCampaign(): void
    {
        if (!\in_array('shadowrun5e', \array_keys(config('app.systems')), true)) {
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

        $expectedOptions = \json_encode([
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
        if (!\in_array('cyberpunkred', \array_keys(config('app.systems')), true)) {
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

        $expectedOptions = \json_encode([
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
        ]);

        $this->actingAs($user)
            ->get(route('campaign.gm-screen', $campaign))
            ->assertOk()
            ->assertSee((string)$character, false);
    }
}
