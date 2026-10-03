<?php

declare(strict_types=1);

namespace Modules\Dnd5e\Tests\Feature\Http\Controllers;

use App\Models\Campaign;
use App\Models\User;
use Modules\Dnd5e\Models\Character;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('dnd5e')]
#[Medium]
final class CharactersControllerTest extends TestCase
{
    /**
     * Test loading D&D 5E characters if unauthenticated.
     */
    public function testUnauthenticated(): void
    {
        $this->getJson(route('dnd5e.characters.index'))
            ->assertUnauthorized();
    }

    /**
     * Test loading D&D 5E characters if authenticated but the user doesn't have
     * any characters.
     */
    public function testAuthenticatedNoCharacters(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('dnd5e.characters.index'))
            ->assertOk()
            ->assertJson(['data' => []]);
    }

    /**
     * Test loading D&D 5E characters if authenticated, but all of the user's
     * characters are for different systems.
     */
    public function testAuthenticatedNoCharactersFromSystem(): void
    {
        $user = User::factory()->create();
        $character = Character::factory()->create([
            'owner' => $user->email->address,
            'system' => 'cyberpunkred',
        ]);
        self::actingAs($user)
            ->getJson(route('dnd5e.characters.index'))
            ->assertOk()
            ->assertJson(['data' => []]);

        $character->delete();
    }

    /**
     * Test loading D&D 5E characters if authenticated, and the user has a
     * character for D&D 5E (in addition to another system).
     */
    public function testAuthenticatedWithSR5ECharacter(): void
    {
        $user = User::factory()->create();
        $character1 = Character::factory()->create([
            'owner' => $user->email->address,
            'system' => 'shadowrun6e',
        ]);
        $character2 = Character::factory()->create([
            'owner' => $user->email->address,
            'system' => 'dnd5e',
        ]);
        self::actingAs($user)
            ->getJson(route('dnd5e.characters.index'))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $character2->_id,
                'name' => $character2->name,
                'owner' => [
                    'id' => $user->id,
                    'name' => $user->name,
                ],
                'system' => 'dnd5e',
            ]);

        $character1->delete();
        $character2->delete();
    }

    public function testShowCharacter(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create(['system' => 'dnd5e']);
        $character = Character::factory()->create([
            'campaign_id' => $campaign->id,
            'owner' => $user->email->address,
            'system' => 'dnd5e',
        ]);

        self::actingAs($user)
            ->getJson(route('dnd5e.characters.show', $character->id))
            ->assertOk()
            ->assertJsonFragment([
                'campaign_id' => $campaign->id,
                'id' => $character->id,
                'name' => $character->name,
                'owner' => [
                    'id' => $user->id,
                    'name' => $user->name,
                ],
                'system' => 'dnd5e',
            ]);

        $character->delete();
    }

    /**
     * Test loading an individual character from a different system.
     */
    public function testShowCharacterOtherSystem(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Character */
        $character = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun6e',
        ]);
        self::actingAs($user)
            ->getJson(route('dnd5e.characters.show', $character->id))
            ->assertNotFound();

        $character->delete();
    }

    public function testViewCharacter(): void
    {
        $user = User::factory()->create();

        /** @var Character */
        $character = Character::factory()->create(['owner' => $user->email]);

        self::actingAs($user)
            ->get(route('dnd5e.character', $character))
            ->assertSee($user->email->address)
            ->assertSee(e($character->name), false);
        $character->delete();
    }
}
