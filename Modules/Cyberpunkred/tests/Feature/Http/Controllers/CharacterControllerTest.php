<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Tests\Feature\Http\Controllers;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Cyberpunkred\Models\Character;
use Modules\Cyberpunkred\Models\PartialCharacter;
use Modules\Cyberpunkred\Models\Role\Exec;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function assert;
use function route;
use function session;

#[Group('cyberpunkred')]
#[Medium]
final class CharacterControllerTest extends TestCase
{
    use WithFaker;

    /**
     * Test loading Cyberpunk Red characters if unauthenticated.
     */
    public function testUnauthenticated(): void
    {
        self::getJson(route('cyberpunkred.characters.index'))
            ->assertUnauthorized();
    }

    /**
     * Test loading Cyberpunk Red characters if authenticated but the user
     * doesn't have any characters.
     */
    public function testAuthenticatedNoCharacters(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('cyberpunkred.characters.index'))
            ->assertOk()
            ->assertJson(['data' => []]);
    }

    /**
     * Test loading Cyberpunk Red characters if authenticated, but all of the
     * user's characters are for different systems.
     */
    public function testAuthenticatedNoCharactersFromSystem(): void
    {
        $user = User::factory()->create();
        $character = Character::factory()->create([
            'handle' => __FUNCTION__,
            'owner' => $user->email->address,
            'system' => 'shadowrun5e',
        ]);
        self::actingAs($user)
            ->getJson(route('cyberpunkred.characters.index'))
            ->assertOk()
            ->assertJson(['data' => []]);

        $character->delete();
    }

    /**
     * Test loading Cyberpunk Red characters if authenticated, and the user has
     * a character for Cyberpunk (in addition to another system).
     */
    public function testAuthenticatedWithCyberpunkCharacter(): void
    {
        $user = User::factory()->create();

        $character1 = Character::factory()->create([
            'handle' => __FUNCTION__,
            'owner' => $user->email->address,
            'system' => 'shadowrun6e',
        ]);
        $character2 = Character::factory()->create([
            'handle' => __FUNCTION__,
            'owner' => $user->email->address,
            'system' => 'cyberpunkred',
        ]);

        self::actingAs($user)
            ->getJson(route('cyberpunkred.characters.index'))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $character2->_id,
                'handle' => $character2->handle,
                'owner' => [
                    'id' => $user->id,
                    'name' => $user->name,
                ],
                'system' => 'cyberpunkred',
            ]);

        $character1->delete();
        $character2->delete();
    }

    /**
     * Test loading an individual character.
     */
    public function testShowCharacter(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()->create(['system' => 'cyberpunkred']);

        /** @var Character */
        $character = Character::factory()->create([
            'handle' => __FUNCTION__,
            'owner' => $user->email->address,
            'system' => 'cyberpunkred',
            'campaign_id' => $campaign->id,
            'skills' => [
                'acting' => 2,
            ],
            'lifepath' => [
                'what-valued' => 'Cold, hard eddies',
            ],
            'weapons' => [
                ['id' => 'medium-melee'],
            ],
        ]);

        self::actingAs($user)
            ->getJson(route('cyberpunkred.characters.show', $character->id))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $character->_id,
                'handle' => $character->handle,
                'owner' => [
                    'id' => $user->id,
                    'name' => $user->name,
                ],
                'system' => 'cyberpunkred',
            ]);

        $character->delete();
    }

    /**
     * Test loading an individual character from another system.
     */
    public function testShowCharacterOtherSystem(): void
    {
        $user = User::factory()->create();

        /** @var Character */
        $character = Character::factory()->create([
            'handle' => __FUNCTION__,
            'owner' => $user->email->address,
            'system' => 'shadowrun6e',
        ]);

        self::actingAs($user)
            ->getJson(route('cyberpunkred.characters.show', $character->id))
            ->assertNotFound();

        $character->delete();
    }

    /**
     * Test loading a character view.
     */
    public function testViewCharacter(): void
    {
        $user = User::factory()->create();
        $character = Character::factory()->create([
            'handle' => __FUNCTION__,
            'owner' => $user->email->address,
            'system' => 'cyberpunkred',
        ]);

        self::actingAs($user)
            ->get(route('cyberpunkred.character', $character->id))
            ->assertSee($user->email->address)
            ->assertSee(e($character->handle), false);

        $character->delete();
    }

    /**
     * Test trying to create a brand new character.
     */
    public function testCreateNewCharacter(): void
    {
        $user = User::factory()->create();

        $characters = PartialCharacter::where('owner', $user->email->address)
            ->get();
        self::assertCount(0, $characters);
        self::actingAs($user)
            ->get(route('cyberpunkred.create'))
            ->assertOk()
            ->assertSee('Name your character');

        $characters = PartialCharacter::where('owner', $user->email->address)
            ->get();
        self::assertCount(1, $characters);

        $character = $characters[0];
        self::assertNotNull($character);
        $character->delete();
    }

    /**
     * Test getting the option to continue an existing character.
     */
    public function testCreateNewCharacterWithExisting(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()->create([
            'handle' => __FUNCTION__,
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->get(route('cyberpunkred.create'))
            ->assertOk()
            ->assertSee(__FUNCTION__);

        $character->delete();
    }

    /**
     * Test trying to continue a character.
     */
    public function testContinueCharacter(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()->create([
            'handle' => __FUNCTION__,
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->get(route('cyberpunkred.create', $character->id))
            ->assertOk()
            ->assertSee('Choose role');

        $character->delete();
    }

    /**
     * Test changing from a previously started character to a new one.
     */
    public function testSwitchCharacter(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()->create([
            'handle' => __FUNCTION__,
            'owner' => $user->email->address,
        ]);
        session(['cyberpunkred-partial' => $character->id]);

        $characters = PartialCharacter::where('owner', $user->email->address)
            ->get();
        self::assertCount(1, $characters);
        self::actingAs($user)
            ->get(route('cyberpunkred.create', 'new'))
            ->assertOk()
            ->assertSee('Name your character');
        $characters = PartialCharacter::where('owner', $user->email->address)
            ->get();
        self::assertCount(2, $characters);

        $character = $characters[0];
        assert($character instanceof PartialCharacter);
        $character->delete();

        $character = $characters[1];
        assert($character instanceof PartialCharacter);
        $character->delete();
    }

    /**
     * Test saving a character for later.
     */
    public function testSaveForLater(): void
    {
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'handle' => __FUNCTION__,
            'owner' => $user->email->address,
        ]);
        session(['cyberpunkred-partial' => $character->id]);

        self::actingAs($user)
            ->get(route('cyberpunkred.create', 'save'))
            ->assertOk()
            ->assertSee('Create a new Cyberpunk Red character')
            ->assertSessionMissing('cyberpunkred-partial');

        $character->delete();
    }

    /**
     * Test trying to name a character.
     */
    public function testNameCharacter(): void
    {
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
        ]);
        session(['cyberpunkred-partial' => $character->id]);

        $name = $this->faker->name;
        self::actingAs($user)
            ->post(
                route('cyberpunkred.create-handle'),
                ['handle' => $name]
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('cyberpunkred.create', 'role'));
        $character->refresh();

        self::assertSame($name, $character->handle);
        $character->delete();
    }

    /**
     * Test loading the role page if the character doesn't have a role yet.
     */
    public function testLoadRolePage(): void
    {
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'handle' => __FUNCTION__,
            'owner' => $user->email->address,
        ]);
        session(['cyberpunkred-partial' => $character->id]);

        self::actingAs($user)
            ->get(route('cyberpunkred.create', 'role'))
            ->assertOk()
            ->assertSee('Choose role');

        $character->delete();
    }

    /**
     * Test loading the role page if the character has chosen a role.
     */
    public function testLoadRolePageAlreadyChosen(): void
    {
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'handle' => __FUNCTION__,
            'owner' => $user->email->address,
            'roles' => [
                [
                    'role' => 'Fixer',
                    'rank' => 4,
                ],
            ],
        ]);
        session(['cyberpunkred-partial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/cyberpunkred/create/role')
            ->assertOk()
            ->assertSee('selected  value="Fixer"', false);

        $character->delete();
    }

    /**
     * Test trying to give a new character a role.
     */
    public function testAssignRole(): void
    {
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'handle' => $this->faker->name,
            'owner' => $user->email->address,
            'roles' => [
                [
                    'role' => 'fixer',
                    'rank' => 4,
                ],
            ],
        ]);
        session(['cyberpunkred-partial' => $character->id]);

        self::actingAs($user)
            ->post(
                route('cyberpunkred.create-role'),
                ['role' => 'Exec']
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('cyberpunkred.create', 'lifepath'));

        $character->refresh();
        $roles = $character->getRoles();
        self::assertInstanceOf(Exec::class, $roles[0]);

        $character->delete();
    }

    /**
     * Test loading the lifepath page.
     */
    public function testLoadLifepathPage(): void
    {
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
        ]);
        session(['cyberpunkred-partial' => $character->id]);

        self::actingAs($user)
            ->get(route('cyberpunkred.create', 'lifepath'))
            ->assertOk()
            ->assertSee('Lifepath is a flowchart');

        $character->delete();
    }

    /**
     * Test loading a role-based lifepath page without having chosen a role.
     */
    public function testLoadRoleBasedLifepathPageWithoutARole(): void
    {
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
        ]);
        session(['cyberpunkred-partial' => $character->id]);

        self::actingAs($user)
            ->get(route('cyberpunkred.create', 'role-based-lifepath'))
            ->assertRedirect('/characters/cyberpunkred/create/role');

        $character->delete();
    }

    /**
     * Test loading a role-based lifepath page.
     */
    public function testLoadRoleBasedLifepathPage(): void
    {
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
            'roles' => [
                [
                    'role' => 'Nomad',
                    'rank' => 4,
                ],
            ],
        ]);
        session(['cyberpunkred-partial' => $character->id]);

        self::actingAs($user)
            ->get(route('cyberpunkred.create', 'role-based-lifepath'))
            ->assertOk()
            ->assertSee('Different Nomad groups');

        $character->delete();
    }

    /**
     * Test updating a character's lifepath.
     */
    public function testAssignLifepath(): void
    {
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'handle' => __FUNCTION__,
            'lifepath' => [
                'affectation' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'background' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'clothing' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'environment' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'family-crisis' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'feeling' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'hair' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'origin' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'person' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'personality' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'possession' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'value' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
            ],
            'owner' => $user->email->address,
            'roles' => [
                [
                    'role' => 'fixer',
                    'rank' => 4,
                ],
            ],
        ]);
        session(['cyberpunkred-partial' => $character->id]);

        self::actingAs($user)
            ->post(
                route('cyberpunkred.create-lifepath'),
                [
                    'affectation' => 2,
                    'background' => 2,
                    'clothing' => 2,
                    'environment' => 2,
                    'family-crisis' => 2,
                    'feeling' => 2,
                    'hair' => 2,
                    'origin' => 2,
                    'person' => 2,
                    'personality' => 2,
                    'possession' => 2,
                    'value' => 2,
                ]
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('cyberpunkred.create', 'stats'));

        $character->refresh();
        foreach ($character->lifepath as $path => $values) {
            self::assertSame(2, $values['chosen']);
        }

        $character->delete();
    }

    /**
     * Test loading the stats page.
     */
    public function testLoadStatsPage(): void
    {
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
        ]);
        session(['cyberpunkred-partial' => $character->id]);

        self::actingAs($user)
            ->get(route('cyberpunkred.create', 'stats'))
            ->assertOk()
            ->assertSee('also called STATs');

        $character->delete();
    }

    /**
     * Test storing a character's stats.
     */
    public function testStoreStats(): void
    {
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'handle' => __FUNCTION__,
            'lifepath' => [],
            'owner' => $user->email->address,
            'roles' => [
                [
                    'role' => 'fixer',
                    'rank' => 4,
                ],
            ],
        ]);
        session(['cyberpunkred-partial' => $character->id]);

        self::assertNull($character->body);
        self::actingAs($user)
            ->post(
                route('cyberpunkred.create-stats'),
                [
                    'body' => 8,
                    'cool' => 2,
                    'dexterity' => 3,
                    'empathy' => 4,
                    'intelligence' => 5,
                    'luck' => 6,
                    'movement' => 7,
                    'reflexes' => 8,
                    'technique' => 2,
                    'willpower' => 3,
                ]
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('cyberpunkred.create', 'review'));

        $character->refresh();
        self::assertSame(8, $character->body);
        self::assertSame(2, $character->cool);
        self::assertSame(3, $character->dexterity);
        self::assertSame(4, $character->empathy);
        self::assertSame(5, $character->intelligence);
        self::assertSame(6, $character->luck);
        self::assertSame(7, $character->movement);
        self::assertSame(8, $character->reflexes);
        self::assertSame(2, $character->technique);
        self::assertSame(3, $character->willpower);

        $character->delete();
    }

    /**
     * Test loading a character that has been all the way through chargen but
     * not saved.
     */
    public function testReview(): void
    {
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'body' => 4,
            'handle' => __FUNCTION__,
            'lifepath' => [
                'affectation' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'background' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'clothing' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'environment' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'family-crisis' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'feeling' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'hair' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'origin' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'person' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'personality' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'possession' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'value' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
            ],
            'owner' => $user->email->address,
            'roles' => [
                [
                    'role' => 'fixer',
                    'rank' => 4,
                ],
            ],
        ]);
        session(['cyberpunkred-partial' => $character->id]);

        self::actingAs($user)
            ->get(route('cyberpunkred.create'))
            ->assertSee('metadata');

        $character->delete();
    }

    /**
     * Test loading a character's review page.
     */
    public function testLoadReview(): void
    {
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'body' => 4,
            'lifepath' => [
                'affectation' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'background' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'clothing' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'environment' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'family-crisis' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'feeling' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'hair' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'origin' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'person' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'personality' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'possession' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'value' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
            ],
            'owner' => $user->email->address,
            'roles' => [
                [
                    'role' => 'fixer',
                    'rank' => 4,
                ],
            ],
        ]);
        session(['cyberpunkred-partial' => $character->id]);

        self::actingAs($user)
            ->get(route('cyberpunkred.create', 'review'))
            ->assertSee('metadata');

        $character->delete();
    }

    /**
     * Test trying to load an invalid character creation page.
     */
    public function testLoadNotFound(): void
    {
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'body' => 4,
            'lifepath' => [
                'affectation' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'background' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'clothing' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'environment' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'family-crisis' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'feeling' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'hair' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'origin' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'person' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'personality' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'possession' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
                'value' => [
                    'rolled' => 1,
                    'chosen' => 1,
                ],
            ],
            'owner' => $user->email->address,
            'roles' => [
                [
                    'role' => 'fixer',
                    'rank' => 4,
                ],
            ],
        ]);
        session(['cyberpunkred-partial' => $character->id]);

        self::actingAs($user)
            ->get(route('cyberpunkred.create', 'augmentations'))
            ->assertNotFound();

        $character->delete();
    }
}
