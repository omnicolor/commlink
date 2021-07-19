<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

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
     * Test an unauthenticated request.
     * @test
     */
    public function testUnauthenticated(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
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
            ->get('/campaigns/create')
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

        // Find a system that doesn't use the options field.
        do {
            $system = $this->faker->randomElement(
                \array_keys(config('app.systems'))
            );
        } while ('shadowrun5e' === $system);

        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->postJson(
                '/campaigns/create',
                [
                    'name' => $name,
                    'system' => $system,
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
                'system' => $system,
            ]
        );
    }

    /**
     * Test creating a new Shadowrun campaign with options.
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
                '/campaigns/create',
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
}
