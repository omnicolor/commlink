<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Character;
use App\Models\User;
use Illuminate\Http\Response;

/**
 * Tests for the main dashboard.
 * @group controllers
 */
final class DashboardControllerTest extends \Tests\TestCase
{
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
    public function testAuthenticatedNoCharacters(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get('/dashboard')
            ->assertSee($user->email)
            ->assertSee('You don\'t have any characters!', false);
    }

    /**
     * Test an authenticated request that has characters.
     * @test
     */
    public function testAuthenticated(): void
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
            ->assertSee($character1->type)
            ->assertSee($character2->handle)
            ->assertSee($character2->type);
    }
}
