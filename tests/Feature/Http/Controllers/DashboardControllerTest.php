<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\Campaign;
use App\Models\Character;
use App\Models\User;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function config;

#[Medium]
final class DashboardControllerTest extends TestCase
{
    public function testUnauthenticated(): void
    {
        self::get('/dashboard')->assertRedirect('/login');
    }

    public function testAuthenticatedNoCharactersNoCampaigns(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->get(route('dashboard'))
            ->assertSee($user->email->address)
            ->assertSee('You don\'t have any characters!', false)
            ->assertSee('You don\'t have any campaigns!', false);
    }

    public function testAuthenticatedWithCharacters(): void
    {
        $user = User::factory()->create();
        $character1 = Character::factory()->create([
            'owner' => $user->email->address,
        ]);
        $character2 = Character::factory()->create([
            'owner' => $user->email->address,
            'system' => 'shadowrun6e',
        ]);
        self::actingAs($user)
            ->get(route('dashboard'))
            ->assertSee($user->email->address)
            ->assertSee((string)$character1->handle)
            ->assertSee(config('commlink.systems')[$character1->system])
            ->assertSee((string)$character2->handle)
            ->assertSee(config('commlink.systems')[$character2->system]);
        $character1->delete();
        $character2->delete();
    }

    public function testWithRegisteredCampaigns(): void
    {
        $user = User::factory()->create();
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
     * Test the dashboard with a gamemastering campaign.
     */
    public function testWithGamemasterCampaign(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create(['gm' => $user]);
        self::actingAs($user)
            ->get(route('dashboard'))
            ->assertSee($campaign->name)
            ->assertDontSee('Registered')
            ->assertSee('Gamemaster');
    }
}
