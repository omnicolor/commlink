<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\CyberpunkRed;

use App\Models\CyberpunkRed\Character;
use App\Models\CyberpunkRed\PartialCharacter;
use App\Models\CyberpunkRed\Role\Exec;
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
        $this->getJson(route('cyberpunkred.characters.index'))
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
        /** @var User */
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
        /** @var User */
        $user = User::factory()->create();
        $this->characters[] = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun6e',
        ]);
        /** @var Character */
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
            'owner' => $user->email,
            'system' => 'shadowrun6e',
        ]);
        $this->actingAs($user)
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
            'owner' => $user->email,
            'system' => 'cyberpunkred',
        ]);
        $view = $this->actingAs($user)
            ->get(
                \sprintf('/characters/cyberpunkred/%s', $character->id),
                ['character' => $character, 'user' => $user]
            );
        $view->assertSee($user->email);
        $view->assertSee(e($character->handle), false);
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
        $this->actingAs($user)
            ->get('/characters/cyberpunkred/create')
            ->assertOk()
            ->assertSee('Name your character');
        $characters = PartialCharacter::where('owner', $user->email)->get();
        self::assertCount(1, $characters);
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
        $character = PartialCharacter::factory()->create([
            'handle' => 'Terrible name',
            'owner' => $user->email,
        ]);

        $this->actingAs($user)
            ->get('/characters/cyberpunkred/create')
            ->assertOk()
            ->assertSee('Terrible name');
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
        $character = PartialCharacter::factory()->create([
            'handle' => 'Terrible name',
            'owner' => $user->email,
        ]);

        $this->actingAs($user)
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
        $character = PartialCharacter::factory()->create([
            'handle' => 'Terrible name',
            'owner' => $user->email,
        ]);
        session(['cyberpunkredpartial' => $character->id]);

        $characters = PartialCharacter::where('owner', $user->email)->get();
        self::assertCount(1, $characters);
        $this->actingAs($user)
            ->get('/characters/cyberpunkred/create/new')
            ->assertOk()
            ->assertSee('Name your character');
        $characters = PartialCharacter::where('owner', $user->email)->get();
        self::assertCount(2, $characters);
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
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
        ]);
        session(['cyberpunkredpartial' => $character->id]);

        $name = $this->faker->name;
        $this->actingAs($user)
            ->post(
                route('cyberpunkred-create-handle'),
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
     * Test loading the role page.
     * @test
     */
    public function testLoadRolePage(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'handle' => 'Name goes here',
            'owner' => $user->email,
            'role' => 'fixer',
        ]);
        session(['cyberpunkredpartial' => $character->id]);

        $this->actingAs($user)
            ->get('/characters/cyberpunkred/create/role')
            ->assertOk()
            ->assertSee('Choose role');
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
        $character = PartialCharacter::factory()->create([
            'handle' => 'Your Name',
            'owner' => $user->email,
            'roles' => [
                [
                    'role' => 'fixer',
                    'rank' => 4,
                ],
            ],
        ]);
        session(['cyberpunkredpartial' => $character->id]);

        $this->actingAs($user)
            ->post(
                route('cyberpunkred-create-role'),
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
}
