<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\Campaign;
use App\Models\Character;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for the main dashboard.
 * @group controllers
 * @medium
 */
final class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

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
        /** @var User */
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
        /** @var User */
        $user = User::factory()->create();
        /** @var Character */
        $character1 = Character::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        /** @var Character */
        $character2 = Character::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        $this->actingAs($user)
            ->get('/dashboard')
            ->assertSee($user->email)
            ->assertSee($character1->handle)
            ->assertSee(config('app.systems')[$character1->system])
            ->assertSee($character2->handle)
            ->assertSee(config('app.systems')[$character2->system]);
        $character1->delete();
        $character2->delete();
    }

    /**
     * Test an authenticated request that has registered campaigns.
     * @test
     */
    public function testWithRegisteredCampaigns(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'registered_by' => $user,
            'gm' => null,
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
        /** @var User */
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()->create(['gm' => $user]);
        $this->actingAs($user)
            ->get('/dashboard')
            ->assertSee($campaign->name)
            ->assertDontSee('Registered')
            ->assertSee('Gamemaster');
    }
}
