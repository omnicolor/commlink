<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\Campaign;
use App\Models\Character;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Tests for the main dashboard.
 * @group controllers
 * @medium
 */
final class DashboardControllerTest extends \Tests\TestCase
{
    use RefreshDatabase;

    /**
     * Characters we're testing on.
     * @var array<int, Character>
     */
    protected array $characters = [];

    /**
     * Clean up after the tests.
     */
    public function tearDown(): void
    {
        foreach ($this->characters as $key => $character) {
            $character->delete();
            unset($this->characters[$key]);
        }
        parent::tearDown();
    }

    /**
     * Test an unauthenticated request.
     * @test
     */
    public function testUnauthenticated(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    /**
     * Test an authenticated request with no characters.
     * @test
     */
    public function testAuthenticatedNoCharactersNoCampaigns(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get('/dashboard')
            ->assertSee($user->email)
            ->assertSee('You don\'t have any characters!', false)
            ->assertSee('You don\'t have any campaigns!', false);
    }

    /**
     * Test an authenticated request that has characters.
     * @test
     */
    public function testAuthenticatedWithCharacters(): void
    {
        $user = User::factory()->create();
        $character1 = $this->characters[] = Character::factory()
            ->create(['owner' => $user->email]);
        $character2 = $this->characters[] = Character::factory()
            ->create(['owner' => $user->email]);
        $this->actingAs($user)
            ->get('/dashboard')
            ->assertSee($user->email)
            ->assertSee($character1->handle)
            ->assertSee($character1->system)
            ->assertSee($character2->handle)
            ->assertSee($character2->system);
    }

    /**
     * Test an authenticated request that has registered campaigns.
     * @test
     */
    public function testWithRegisteredCampaigns(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create([
            'registered_by' => $user->id,
        ]);
        $this->actingAs($user)
            ->get('/dashboard')
            ->assertSee($campaign->name)
            ->assertSee('Registered')
            ->assertDontSee('Gamemaster');
    }

    /**
     * Test a the dashboard with a gamemastering campaign.
     * @test
     */
    public function testWithGamemasterCampaign(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create(['gm' => $user->id]);
        $this->actingAs($user)
            ->get('/dashboard')
            ->assertSee($campaign->name)
            ->assertDontSee('Registered')
            ->assertSee('Gamemaster');
    }
}
