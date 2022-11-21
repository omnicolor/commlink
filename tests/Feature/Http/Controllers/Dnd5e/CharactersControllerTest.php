<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Dnd5e;

use App\Models\Dnd5e\Character;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for the D&D 5E characters controller.
 * @group dnd5e
 * @group controllers
 * @medium
 */
final class CharactersControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test loading D&D 5E characters if unauthenticated.
     * @test
     */
    public function testUnauthenticated(): void
    {
        $this->getJson(route('dnd5e.characters.index'))
            ->assertUnauthorized();
    }

    /**
     * Test loading D&D 5E characters if authenticated but the user doesn't have
     * any characters.
     * @test
     */
    public function testAuthenticatedNoCharacters(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('dnd5e.characters.index'))
            ->assertOk()
            ->assertJson(['data' => []]);
    }

    /**
     * Test loading D&D 5E characters if authenticated, but all of the user's
     * characters are for different systems.
     * @test
     */
    public function testAuthenticatedNoCharactersFromSystem(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $character = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'cyberpunkred',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        $this->actingAs($user)
            ->getJson(route('dnd5e.characters.index'))
            ->assertOk()
            ->assertJson(['data' => []]);

        $character->delete();
    }

    /**
     * Test loading D&D 5E characters if authenticated, and the user has a
     * character for D&D 5E (in addition to another system).
     * @test
     */
    public function testAuthenticatedWithSR5ECharacter(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Character */
        $character1 = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun6e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        /** @var Character */
        $character2 = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'dnd5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        $this->actingAs($user)
            ->getJson(route('dnd5e.characters.index'))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $character2->_id,
                'name' => $character2->name,
                'owner' => $user->email,
                'system' => 'dnd5e',
                'updated_at' => $character2->updated_at,
                'created_at' => $character2->created_at,
            ]);

        $character1->delete();
        $character2->delete();
    }

    /**
     * Test showing an individual character.
     * @test
     */
    public function testShowCharacter(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Character */
        $character = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'dnd5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        $this->actingAs($user)
            ->getJson(route('dnd5e.characters.show', $character->id))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $character->_id,
                'name' => $character->name,
                'owner' => $user->email,
                'system' => 'dnd5e',
                'updated_at' => $character->updated_at,
                'created_at' => $character->created_at,
            ]);

        $character->delete();
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
        $character = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun6e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        $this->actingAs($user)
            ->getJson(route('dnd5e.characters.show', $character->id))
            ->assertNotFound();

        $character->delete();
    }
}
