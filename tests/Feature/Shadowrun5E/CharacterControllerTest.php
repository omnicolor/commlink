<?php

declare(strict_types=1);

namespace Tests\Feature\Shadowrun5E;

use App\Models\Shadowrun5E\Character;
use App\Models\User;
use Illuminate\Http\Response;

/**
 * Controller for the Shadowrun 5E characters controller.
 * @group shadowrun
 * @group shadowrun5e
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
     * Test loading Shadowrun 5E characters if unauthenticated.
     * @test
     */
    public function testUnauthenticated(): void
    {
        $this->getJson(route('shadowrun5e.characters.index'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test loading Shadowrun 5E characters if authenticated but the user
     * doesn't have any characters.
     * @test
     */
    public function testAuthenticatedNoCharacters(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.characters.index'))
            ->assertOk()
            ->assertJson(['data' => []]);
    }

    /**
     * Test loading Shadowrun 5E characters if authenticated, but all of the
     * user's characters are for different systems.
     * @test
     */
    public function testAuthenticatedNoCharactersFromSystem(): void
    {
        $user = User::factory()->create();
        $character = $this->characters[] = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'cyberpunkred',
        ]);
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.characters.index'))
            ->assertOk()
            ->assertJson(['data' => []]);
        $character->delete();
    }

    /**
     * Test loading Shadowrun 5E characters if authenticated, and the user has
     * a character for SR5E (in addition to another system).
     * @test
     */
    public function testAuthenticatedWithSR5ECharacter(): void
    {
        $user = User::factory()->create();
        $character1 = $this->characters[] = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun6e',
        ]);
        $character2 = $this->characters[] = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
        ]);
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.characters.index'))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $character2->_id,
                'handle' => $character2->handle,
                'owner' => $user->email,
                'system' => 'shadowrun5e',
                'updated_at' => $character2->updated_at->toJson(),
                'created_at' => $character2->created_at->toJson(),
            ]);
        $character1->delete();
        $character2->delete();
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
            'system' => 'shadowrun5e',
        ]);
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.characters.show', $character->id))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $character->_id,
                'handle' => $character->handle,
                'owner' => $user->email,
                'system' => 'shadowrun5e',
                'updated_at' => $character->updated_at->toJson(),
                'created_at' => $character->created_at->toJson(),
            ]);
        $character->delete();
    }

    /**
     * Test loading an individual character.
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
            ->getJson(route('shadowrun5e.characters.show', $character->id))
            ->assertStatus(Response::HTTP_NOT_FOUND);
        $character->delete();
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
            'system' => 'shadowrun5e',
        ]);
        $view = $this->actingAs($user)
            ->get(
                sprintf('/characters/shadowrun5e/%s', $character->id),
                ['character' => $character, 'user' => $user]
            );
        $view->assertSee($user->email);
        $view->assertSee($character->handle);
    }
}
