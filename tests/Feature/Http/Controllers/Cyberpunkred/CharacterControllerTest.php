<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Cyberpunkred;

use App\Models\Cyberpunkred\Character;
use App\Models\Cyberpunkred\PartialCharacter;
use App\Models\Cyberpunkred\Role\Exec;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Controller for the Cyberpunk Red characters controller.
 * @group controllers
 * @group cyberpunkred
 * @medium
 */
final class CharacterControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

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
     * Test loading Cyberpunk Red characters if unauthenticated.
     * @test
     */
    public function testUnauthenticated(): void
    {
        self::getJson(route('cyberpunkred.characters.index'))
            ->assertUnauthorized();
    }

    /**
     * Test loading Cyberpunk Red characters if authenticated but the user
     * doesn't have any characters.
     * @test
     */
    public function testAuthenticatedNoCharacters(): void
    {
        /** @var User */
        $user = User::factory()->create();
        self::actingAs($user)
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
        /** @var User */
        $user = User::factory()->create();
        $this->characters[] = Character::factory()->create([
            'handle' => __FUNCTION__,
            'owner' => $user->email,
            'system' => 'shadowrun5e',
        ]);
        self::actingAs($user)
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
        /** @var User */
        $user = User::factory()->create();
        $this->characters[] = Character::factory()->create([
            'handle' => __FUNCTION__,
            'owner' => $user->email,
            'system' => 'shadowrun6e',
        ]);
        /** @var Character */
        $character2 = $this->characters[] = Character::factory()->create([
            'handle' => __FUNCTION__,
            'owner' => $user->email,
            'system' => 'cyberpunkred',
        ]);
        self::actingAs($user)
            ->getJson(route('cyberpunkred.characters.index'))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $character2->_id,
                'handle' => $character2->handle,
                'owner' => $user->email,
                'system' => 'cyberpunkred',
                'updated_at' => $character2->updated_at,
                'created_at' => $character2->created_at,
            ]);
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
            'handle' => __FUNCTION__,
            'owner' => $user->email,
            'system' => 'cyberpunkred',
        ]);
        self::actingAs($user)
            ->getJson(route('cyberpunkred.characters.show', $character->id))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $character->_id,
                'handle' => $character->handle,
                'owner' => $user->email,
                'system' => 'cyberpunkred',
                'updated_at' => $character->updated_at,
                'created_at' => $character->created_at,
            ]);
    }

    /**
     * Test loading an individual character from another system.
     * @test
     */
    public function testShowCharacterOtherSystem(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Character */
        $character = $this->characters[] = Character::factory()->create([
            'handle' => __FUNCTION__,
            'owner' => $user->email,
            'system' => 'shadowrun6e',
        ]);
        self::actingAs($user)
            ->getJson(route('cyberpunkred.characters.show', $character->id))
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
            'handle' => __FUNCTION__,
            'owner' => $user->email,
            'system' => 'cyberpunkred',
        ]);
        self::actingAs($user)
            ->get(
                \sprintf('/characters/cyberpunkred/%s', $character->id),
                ['character' => $character, 'user' => $user]
            )
            ->assertSee($user->email)
            ->assertSee(e($character->handle), false);
    }

    /**
     * Test trying to create a brand new character.
     * @test
     */
    public function testCreateNewCharacter(): void
    {
        /** @var User */
        $user = User::factory()->create();

        $characters = PartialCharacter::where('owner', $user->email)->get();
        self::assertCount(0, $characters);
        self::actingAs($user)
            ->get('/characters/cyberpunkred/create')
            ->assertOk()
            ->assertSee('Name your character');
        $characters = PartialCharacter::where('owner', $user->email)->get();
        self::assertCount(1, $characters);
        // @phpstan-ignore-next-line
        $this->characters[] = $characters[0];
    }

    /**
     * Test getting the option to continue an existing character.
     * @test
     */
    public function testCreateNewCharacterWithExisting(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = $this->characters[] = PartialCharacter::factory()->create([
            'handle' => __FUNCTION__,
            'owner' => $user->email,
        ]);

        self::actingAs($user)
            ->get('/characters/cyberpunkred/create')
            ->assertOk()
            ->assertSee(__FUNCTION__);
    }

    /**
     * Test trying to continue a character.
     * @test
     */
    public function testContinueCharacter(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = $this->characters[] = PartialCharacter::factory()->create([
            'handle' => __FUNCTION__,
            'owner' => $user->email,
        ]);

        self::actingAs($user)
            ->get(\sprintf(
                '/characters/cyberpunkred/create/%s',
                $character->id
            ))
            ->assertOk()
            ->assertSee('Choose role');
    }

    /**
     * Test changing from a previously started character to a new one.
     * @test
     */
    public function testSwitchCharacter(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = $this->characters[] = PartialCharacter::factory()->create([
            'handle' => __FUNCTION__,
            'owner' => $user->email,
        ]);
        session(['cyberpunkredpartial' => $character->id]);

        $characters = PartialCharacter::where('owner', $user->email)->get();
        self::assertCount(1, $characters);
        self::actingAs($user)
            ->get('/characters/cyberpunkred/create/new')
            ->assertOk()
            ->assertSee('Name your character');
        $characters = PartialCharacter::where('owner', $user->email)->get();
        self::assertCount(2, $characters);
        // @phpstan-ignore-next-line
        $this->characters[] = $characters[1];
    }

    /**
     * Test saving a character for later.
     * @test
     */
    public function testSaveForLater(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = $this->characters[] = PartialCharacter::factory()->create([
            'handle' => __FUNCTION__,
            'owner' => $user->email,
        ]);
        session(['cyberpunkredpartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/cyberpunkred/create/save')
            ->assertOk()
            ->assertSee('Create a new Cyberpunk Red character')
            ->assertSessionMissing('cyberpunkredpartial');
    }

    /**
     * Test trying to name a character.
     * @test
     */
    public function testNameCharacter(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = $this->characters[] = PartialCharacter::factory()->create([
            'owner' => $user->email,
        ]);
        session(['cyberpunkredpartial' => $character->id]);

        $name = $this->faker->name;
        self::actingAs($user)
            ->post(
                route('cyberpunkred.create-handle'),
                ['handle' => $name]
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(
                config('app.url') . '/characters/cyberpunkred/create/role'
            );
        $character->refresh();

        self::assertSame($name, $character->handle);
    }

    /**
     * Test loading the role page if the character doesn't have a role yet.
     * @test
     */
    public function testLoadRolePage(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = $this->characters[] = PartialCharacter::factory()->create([
            'handle' => __FUNCTION__,
            'owner' => $user->email,
        ]);
        session(['cyberpunkredpartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/cyberpunkred/create/role')
            ->assertOk()
            ->assertSee('Choose role');
    }

    /**
     * Test loading the role page if the character has chosen a role.
     * @test
     */
    public function testLoadRolePageAlreadyChosen(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = $this->characters[] = PartialCharacter::factory()->create([
            'handle' => __FUNCTION__,
            'owner' => $user->email,
            'roles' => [
                [
                    'role' => 'Fixer',
                    'rank' => 4,
                ],
            ],
        ]);
        session(['cyberpunkredpartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/cyberpunkred/create/role')
            ->assertOk()
            ->assertSee('selected  value="Fixer"', false);
    }

    /**
     * Test trying to give a new character a role.
     * @test
     */
    public function testAssignRole(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = $this->characters[] = PartialCharacter::factory()->create([
            'handle' => $this->faker->name,
            'owner' => $user->email,
            'roles' => [
                [
                    'role' => 'fixer',
                    'rank' => 4,
                ],
            ],
        ]);
        session(['cyberpunkredpartial' => $character->id]);

        self::actingAs($user)
            ->post(
                route('cyberpunkred.create-role'),
                ['role' => 'Exec']
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(
                config('app.url') . '/characters/cyberpunkred/create/lifepath'
            );

        $character->refresh();
        $roles = $character->getRoles();
        self::assertInstanceOf(Exec::class, $roles[0]);
    }

    /**
     * Test loading the lifepath page.
     * @test
     */
    public function testLoadLifepathPage(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = $this->characters[] = PartialCharacter::factory()->create([
            'owner' => $user->email,
        ]);
        session(['cyberpunkredpartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/cyberpunkred/create/lifepath')
            ->assertOk()
            ->assertSee('Lifepath is a flowchart');
    }

    /**
     * Test loading a role-based lifepath page without having chosen a role.
     * @test
     */
    public function testLoadRoleBasedLifepathPageWithoutARole(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = $this->characters[] = PartialCharacter::factory()->create([
            'owner' => $user->email,
        ]);
        session(['cyberpunkredpartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/cyberpunkred/create/role-based-lifepath')
            ->assertRedirect('/characters/cyberpunkred/create/role');
    }

    /**
     * Test loading a role-based lifepath page.
     * @test
     */
    public function testLoadRoleBasedLifepathPage(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = $this->characters[] = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'roles' => [
                [
                    'role' => 'Nomad',
                    'rank' => 4,
                ],
            ],
        ]);
        session(['cyberpunkredpartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/cyberpunkred/create/role-based-lifepath')
            ->assertOk()
            ->assertSee('Different Nomad groups');
    }

    /**
     * Test updating a character's lifepath.
     * @test
     */
    public function testAssignLifepath(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = $this->characters[] = PartialCharacter::factory()->create([
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
            'owner' => $user->email,
            'roles' => [
                [
                    'role' => 'fixer',
                    'rank' => 4,
                ],
            ],
        ]);
        session(['cyberpunkredpartial' => $character->id]);

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
            ->assertRedirect(
                config('app.url') . '/characters/cyberpunkred/create/stats'
            );

        $character->refresh();
        foreach ($character->lifepath as $path => $values) {
            self::assertSame(2, $values['chosen']);
        }
    }

    /**
     * Test loading the stats page.
     * @test
     */
    public function testLoadStatsPage(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = $this->characters[] = PartialCharacter::factory()->create([
            'owner' => $user->email,
        ]);
        session(['cyberpunkredpartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/cyberpunkred/create/stats')
            ->assertOk()
            ->assertSee('also called STATs');
    }

    /**
     * Test storing a character's stats.
     * @test
     */
    public function testStoreStats(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = $this->characters[] = PartialCharacter::factory()->create([
            'handle' => __FUNCTION__,
            'lifepath' => [],
            'owner' => $user->email,
            'roles' => [
                [
                    'role' => 'fixer',
                    'rank' => 4,
                ],
            ],
        ]);
        session(['cyberpunkredpartial' => $character->id]);

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
            ->assertRedirect(
                config('app.url') . '/characters/cyberpunkred/create/review'
            );

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
    }

    /**
     * Test loading a character that has been all the way through chargen but
     * not saved.
     * @test
     */
    public function testReview(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = $this->characters[] = PartialCharacter::factory()->create([
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
            'owner' => $user->email,
            'roles' => [
                [
                    'role' => 'fixer',
                    'rank' => 4,
                ],
            ],
        ]);
        session(['cyberpunkredpartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/cyberpunkred/create')
            ->assertSee('metadata');
    }

    /**
     * Test loading a character's review page.
     * @test
     */
    public function testLoadReview(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = $this->characters[] = PartialCharacter::factory()->create([
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
            'owner' => $user->email,
            'roles' => [
                [
                    'role' => 'fixer',
                    'rank' => 4,
                ],
            ],
        ]);
        session(['cyberpunkredpartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/cyberpunkred/create/review')
            ->assertSee('metadata');
    }

    /**
     * Test trying to load an invalid character creation page.
     * @test
     */
    public function testLoadNotFound(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = $this->characters[] = PartialCharacter::factory()->create([
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
            'owner' => $user->email,
            'roles' => [
                [
                    'role' => 'fixer',
                    'rank' => 4,
                ],
            ],
        ]);
        session(['cyberpunkredpartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/cyberpunkred/create/augmentations')
            ->assertNotFound();
    }
}
