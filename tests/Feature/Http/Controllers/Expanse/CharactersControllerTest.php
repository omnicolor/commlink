<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Expanse;

use App\Models\Expanse\Character;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;

/**
 * Tests for the Expanse character controller.
 * @covers \App\Http\Controllers\Expanse\CharactersController
 * @covers \App\Http\Resources\Expanse\CharacterResource
 * @covers \App\Models\Character::newFromBuilder
 * @covers \App\Models\Expanse\Character::booted
 * @group expanse
 * @group controllers
 * @medium
 */
final class CharactersControllerTest extends \Tests\TestCase
{
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
     * Test loading Expanse characters if unauthenticated.
     * @test
     */
    public function testUnauthenticated(): void
    {
        $this->getJson(route('expanse.characters.index'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
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
        $character = $this->characters[] = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'cyberpunkred',
        ]);
        $this->actingAs($user)
            ->getJson(route('expanse.characters.index'))
            ->assertOk()
            ->assertJson(['data' => []]);
        // @phpstan-ignore-next-line
        $character->delete();
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
        $this->characters[] = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun6e',
        ]);
        /** @var Character */
        $character = $this->characters[] = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'expanse',
        ]);
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
        /** @var Character */
        $character = $this->characters[] = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun6e',
        ]);
        $this->actingAs($user)
            ->getJson(route('expanse.characters.show', $character))
            ->assertStatus(Response::HTTP_NOT_FOUND);
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
            'system' => 'expanse',
        ]);
        $view = $this->actingAs($user)
            ->get(
                \sprintf('/characters/expanse/%s', $character->id),
                ['character' => $character, 'user' => $user]
            );
        $view->assertSee($user->email);
        $view->assertSee($character->name);
    }
}
