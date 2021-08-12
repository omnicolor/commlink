<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Shadowrun5E;

use App\Models\Shadowrun5E\Character;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

/**
 * Controller for the Shadowrun 5E characters controller.
 * @covers \App\Http\Controllers\Shadowrun5E\CharactersController
 * @covers \App\Http\Resources\Shadowrun5E\CharacterResource
 * @covers \App\Models\Character::newFromBuilder
 * @covers \App\Models\Shadowrun5E\Character::booted
 * @group shadowrun
 * @group shadowrun5e
 * @group controllers
 * @medium
 */
final class CharacterControllerTest extends \Tests\TestCase
{
    use RefreshDatabase;

    /**
     * Characters we're testing on.
     * @var array<int, Character|Collection|Model>
     */
    protected array $characters = [];

    /**
     * Clean up after the tests.
     */
    public function tearDown(): void
    {
        foreach ($this->characters as $key => $character) {
            // @phpstan-ignore-next-line
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
        /** @var User */
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
        /** @var User */
        $user = User::factory()->create();
        $this->characters[] = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'cyberpunkred',
        ]);
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.characters.index'))
            ->assertOk()
            ->assertJson(['data' => []]);
    }

    /**
     * Test loading Shadowrun 5E characters if authenticated, and the user has
     * a character for SR5E (in addition to another system).
     * @test
     */
    public function testAuthenticatedWithSR5ECharacter(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Character */
        $character1 = $this->characters[] = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun6e',
        ]);
        /** @var Character */
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
                'updated_at' => $character2->updated_at,
                'created_at' => $character2->created_at,
            ]);
    }

    /**
     * Test listing a user's Shadowrun characters.
     * @test
     */
    public function testListCharacters(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $view = $this->actingAs($user)
            ->get('/characters/shadowrun5e')
            ->assertSee('You don\'t have any characters!', false)
            ->assertSee('Shadowrun 5E Characters');
    }

    /**
     * Test loading an individual character.
     * @test
     */
    public function testShowCharacter(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Character */
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
                'updated_at' => $character->updated_at,
                'created_at' => $character->created_at,
            ]);
    }

    /**
     * Test loading an individual character from a different system.
     * @test
     */
    public function testShowCharacterOtherSystem(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Character */
        $character = $this->characters[] = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun6e',
        ]);
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.characters.show', $character->id))
            ->assertNotFound();
    }

    /**
     * Test loading a character view.
     * @test
     */
    public function testViewCharacter(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Character */
        $character = $this->characters[] = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
        ]);
        $this->actingAs($user)
            ->get(
                \sprintf('/characters/shadowrun5e/%s', $character->id),
                ['character' => $character, 'user' => $user]
            )
            ->assertSee($character->handle);
    }
}
