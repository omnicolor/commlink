<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Expanse;

use App\Models\Campaign;
use App\Models\Cyberpunkred\Character as CprCharacter;
use App\Models\Expanse\Character;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @group controllers
 * @group expanse
 * @medium
 */
final class CharactersControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test loading Expanse characters if unauthenticated.
     * @test
     */
    public function testUnauthenticated(): void
    {
        self::getJson(route('expanse.characters.index'))->assertUnauthorized();
    }

    /**
     * Test loading Expanse characters if authenticated but the user doesn't
     * have any characters.
     * @test
     */
    public function testAuthenticatedNoCharacters(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
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
        $user = User::factory()->create();

        CprCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        self::actingAs($user)
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
        $user = User::factory()->create();

        CprCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        /** @var Character */
        $character = Character::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        self::actingAs($user)
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

        $character->delete();
    }

    /**
     * Test listing a user's Expanse characters if they have none.
     * @test
     */
    public function testListCharactersIfTheyHaveNone(): void
    {
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
        $user = User::factory()->create();

        /** @var Character */
        $character = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'expanse',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        self::actingAs($user)
            ->get('/characters/expanse')
            ->assertDontSee('You don\'t have any characters!', false)
            ->assertSee('Expanse Characters');

        $character->delete();
    }

    /**
     * Test loading an individual character.
     * @test
     */
    public function testShowCharacter(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()->create(['system' => 'expanse']);

        /** @var Character */
        $character = Character::factory()->create([
            'focuses' => [
                ['id' => 'crafting'],
            ],
            'owner' => $user->email,
            'system' => 'expanse',
            'campaign_id' => $campaign->id,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        self::actingAs($user)
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

        $character->delete();
    }

    /**
     * Test loading an individual character for another system.
     * @test
     */
    public function testShowCharacterOtherSystem(): void
    {
        $user = User::factory()->create();

        /** @var CprCharacter */
        $character = CprCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        self::actingAs($user)
            ->getJson(route('expanse.characters.show', $character))
            ->assertNotFound();

        $character->delete();
    }

    /**
     * Test loading a character view.
     * @test
     */
    public function testViewCharacter(): void
    {
        $user = User::factory()->create();

        /** @var Character */
        $character = Character::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        self::actingAs($user)
            ->get(
                \sprintf('/characters/expanse/%s', $character->id),
                ['character' => $character, 'user' => $user]
            )
            ->assertSee($user->email)
            ->assertSee(e($character->name), false);

        $character->delete();
    }
}
