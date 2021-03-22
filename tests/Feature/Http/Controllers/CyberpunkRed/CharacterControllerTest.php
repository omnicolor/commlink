<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\CyberpunkRed;

use App\Models\CyberpunkRed\Character;
use App\Models\User;
use Illuminate\Http\Response;

/**
 * Controller for the Cyberpunk Red characters controller.
 * @covers \App\Http\Controllers\CyberpunkRed\CharactersController
 * @covers \App\Http\Resources\CyberpunkRed\CharacterResource
 * @covers \App\Models\Character::newFromBuilder
 * @covers \App\Models\CyberpunkRed\Character::booted
 * @group cyberpunkred
 * @group controllers
 */
final class CharacterControllerTest extends \Tests\TestCase
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
     * Test loading Cyberpunk Red characters if unauthenticated.
     * @test
     */
    public function testUnauthenticated(): void
    {
        $this->getJson(route('cyberpunkred.characters.index'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test loading Cyberpunk Red characters if authenticated but the user
     * doesn't have any characters.
     * @test
     */
    public function testAuthenticatedNoCharacters(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('cyberpunkred.characters.index'))
            ->assertOk()
            ->assertJson(['data' => []]);
    }

    /**
     * Test loading Cyberpunk Red characters if authenticated, but all of the
     * user's characters are for different systems.
     * @test
     */
    public function testAuthenticatedNoCharactersFromSystem(): void
    {
        $user = User::factory()->create();
        $this->characters[] = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
        ]);
        $this->actingAs($user)
            ->getJson(route('cyberpunkred.characters.index'))
            ->assertOk()
            ->assertJson(['data' => []]);
    }

    /**
     * Test loading Cyberpunk Red characters if authenticated, and the user has
     * a character for Cyberpunk (in addition to another system).
     * @test
     */
    public function testAuthenticatedWithCyberpunkCharacter(): void
    {
        $user = User::factory()->create();
        $this->characters[] = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun6e',
        ]);
        $character2 = $this->characters[] = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'cyberpunkred',
        ]);
        $this->actingAs($user)
            ->getJson(route('cyberpunkred.characters.index'))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $character2->_id,
                'handle' => $character2->handle,
                'owner' => $user->email,
                'system' => 'cyberpunkred',
                'updated_at' => $character2->updated_at->toJson(),
                'created_at' => $character2->created_at->toJson(),
            ]);
    }

    /**
     * Test loading an individual character.
     * @test
     */
    public function testShowCharacter(): void
    {
        $user = User::factory()->create();
        $character = $this->characters[] = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'cyberpunkred',
        ]);
        $this->actingAs($user)
            ->getJson(route('cyberpunkred.characters.show', $character->id))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $character->_id,
                'handle' => $character->handle,
                'owner' => $user->email,
                'system' => 'cyberpunkred',
                'updated_at' => $character->updated_at->toJson(),
                'created_at' => $character->created_at->toJson(),
            ]);
    }

    /**
     * Test loading an individual character from another system.
     * @test
     */
    public function testShowCharacterOtherSystem(): void
    {
        $user = User::factory()->create();
        $character = $this->characters[] = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun6e',
        ]);
        $this->actingAs($user)
            ->getJson(route('cyberpunkred.characters.show', $character->id))
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * Test loading a character view.
     * @test
     */
    public function testViewCharacter(): void
    {
        $user = User::factory()->create();
        $character = $this->characters[] = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'cyberpunkred',
        ]);
        $view = $this->actingAs($user)
            ->get(
                sprintf('/characters/cyberpunkred/%s', $character->id),
                ['character' => $character, 'user' => $user]
            );
        $view->assertSee($user->email);
        $view->assertSee($character->handle);
    }
}
