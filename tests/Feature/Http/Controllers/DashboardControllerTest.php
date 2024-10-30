<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\Campaign;
use App\Models\Character;
use App\Models\User;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Medium]
final class DashboardControllerTest extends TestCase
{
    /**
     * Test an unauthenticated request.
     */
    public function testUnauthenticated(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    /**
     * Test an authenticated request with no characters.
     */
    public function testAuthenticatedNoCharactersNoCampaigns(): void
    {
        /** @var User */
        $user = User::factory()->create();
        self::actingAs($user)
            ->get(route('dashboard'))
            ->assertSee($user->email)
            ->assertSee('You don\'t have any characters!', false)
            ->assertSee('You don\'t have any campaigns!', false);
    }

    /**
     * Test an authenticated request that has characters.
     */
    public function testAuthenticatedWithCharacters(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Character */
        $character1 = Character::factory()->create(['owner' => $user->email]);
        /** @var Character */
        $character2 = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun6e',
        ]);
        self::actingAs($user)
            ->get(route('dashboard'))
            ->assertSee($user->email)
            ->assertSee((string)$character1->handle)
            ->assertSee(config('commlink.systems')[$character1->system])
            ->assertSee((string)$character2->handle)
            ->assertSee(config('commlink.systems')[$character2->system]);
        $character1->delete();
        $character2->delete();
    }

    /**
     * Test an authenticated request that has registered campaigns.
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
        self::actingAs($user)
            ->get(route('dashboard'))
            ->assertSee($campaign->name)
            ->assertSee('Registered')
            ->assertDontSee('Gamemaster');
    }

    /**
     * Test a the dashboard with a gamemastering campaign.
     */
    public function testWithGamemasterCampaign(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()->create(['gm' => $user]);
        self::actingAs($user)
            ->get(route('dashboard'))
            ->assertSee($campaign->name)
            ->assertDontSee('Registered')
            ->assertSee('Gamemaster');
    }
}
