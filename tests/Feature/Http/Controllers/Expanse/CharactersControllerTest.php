<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Expanse;

use App\Models\CyberpunkRed\Character as CprCharacter;
use App\Models\Expanse\Character;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Tests for the Expanse character controller.
 * @group expanse
 * @group controllers
 * @medium
 */
final class CharactersControllerTest extends \Tests\TestCase
{
    use RefreshDatabase;

    /**
     * Test loading Expanse characters if unauthenticated.
     * @test
     */
    public function testUnauthenticated(): void
    {
        $this->getJson(route('expanse.characters.index'))->assertUnauthorized();
    }

    /**
     * Test loading Expanse characters if authenticated but the user doesn't
     * have any characters.
     * @test
     */
    public function testAuthenticatedNoCharacters(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('expanse.characters.index'))
            ->assertOk()
            ->assertJson(['data' => []]);
    }

    /**
     * Test loading Expanse characters if authenticated, but all of the user's
     * characters are for different systems.
     * @test
     */
    public function testAuthenticatedNoCharactersFromSystem(): void
    {
        /** @var User */
        $user = User::factory()->create();

        CprCharacter::factory()->create(['owner' => $user->email]);

        $this->actingAs($user)
            ->getJson(route('expanse.characters.index'))
            ->assertOk()
            ->assertJson(['data' => []]);
    }

    /**
     * Test loading Expanse characters if authenticated, and the user has a
     * character for SR5E (in addition to another system).
     * @test
     */
    public function testAuthenticatedWithCharacters(): void
    {
        /** @var User */
        $user = User::factory()->create();

        CprCharacter::factory()->create(['owner' => $user->email]);

        /** @var Character */
        $character = Character::factory()->create(['owner' => $user->email]);

        $this->actingAs($user)
            ->getJson(route('expanse.characters.index'))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $character->_id,
                'name' => $character->name,
                'owner' => $user->email,
                'system' => 'expanse',
                'updated_at' => $character->updated_at,
                'created_at' => $character->created_at,
            ]);
    }

    /**
     * Test listing a user's Expanse characters if they have none.
     * @test
     */
    public function testListCharactersIfTheyHaveNone(): void
    {
        /** @var User */
        $user = User::factory()->create();
        self::actingAs($user)
            ->get('/characters/expanse')
            ->assertSee('You don\'t have any characters!', false)
            ->assertSee('Expanse Characters');
    }

    /**
     * Test listing a user's Expanse characters if they've got at least one.
     * @test
     */
    public function testListCharacters(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Character */
        $character = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'expanse',
        ]);

        self::actingAs($user)
            ->get('/characters/expanse')
            ->assertDontSee('You don\'t have any characters!', false)
            ->assertSee('Expanse Characters');
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
        $character = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'expanse',
        ]);

        $this->actingAs($user)
            ->getJson(route('expanse.characters.show', $character))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $character->_id,
                'name' => $character->name,
                'owner' => $user->email,
                'system' => 'expanse',
                'updated_at' => $character->updated_at,
                'created_at' => $character->created_at,
            ]);
    }

    /**
     * Test loading an individual character for another system.
     * @test
     */
    public function testShowCharacterOtherSystem(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var CprCharacter */
        $character = CprCharacter::factory()->create(['owner' => $user->email]);

        $this->actingAs($user)
            ->getJson(route('expanse.characters.show', $character))
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
        $character = Character::factory()->create(['owner' => $user->email]);

        $this->actingAs($user)
            ->get(
                \sprintf('/characters/expanse/%s', $character->id),
                ['character' => $character, 'user' => $user]
            )
            ->assertSee($user->email)
            ->assertSee(e($character->name), false);
    }
}
