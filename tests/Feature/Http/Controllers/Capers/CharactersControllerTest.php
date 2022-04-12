<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Capers;

use App\Models\Capers\Character;
use App\Models\Capers\Identity;
use App\Models\Capers\PartialCharacter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Tests for the Capers character controller.
 * @group capers
 * @group controllers
 * @group current
 * @medium
 */
final class CharactersControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

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
                route('capers.character', $character),
                ['character' => $character, 'user' => $user]
            )
            ->assertSee($user->email)
            ->assertSee(e($character->name), false);
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
        ]);
        $this->actingAs($user)
            ->getJson(route('capers.character', $character))
            ->assertNotFound();
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
            ->get('/characters/capers/create')
            ->assertOk()
            ->assertSee('The basics');
        $characters = PartialCharacter::where('owner', $user->email)->get();
        self::assertCount(1, $characters);
    }

    /**
     * Test trying to resume building a character if the user has multiple.
     * @test
     */
    public function testCreateNewCharacterChoose(): void
    {
        /** @var User */
        $user = User::factory()->create();

        PartialCharacter::factory()->create(['owner' => $user->email]);
        PartialCharacter::factory()->create(['owner' => $user->email]);

        $this->actingAs($user)
            ->get('/characters/capers/create')
            ->assertOk()
            ->assertSee('Choose character');
    }

    /**
     * Test choosing which character to continue.
     * @test
     */
    public function testCreateNewCharacterContinue(): void
    {
        /** @var User */
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
        ]);

        $this->actingAs($user)
            ->get(sprintf('/characters/capers/create/%s', $character->id))
            ->assertRedirect('/characters/capers/create/basics');
    }

    /**
     * Test trying to set the basics for a character without sending any.
     * @test
     */
    public function testCreateBasicsEmpty(): void
    {
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('capers.create-basics'), [])
            ->assertSessionHasErrors(['name', 'nav', 'type']);
    }

    /**
     * Test trying to set the basics for a character.
     * @test
     */
    public function testCreateBasics(): void
    {
        /** @var User */
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
        ]);
        session(['capers-partial' => $character->id]);

        $name = $this->faker->name;
        $this->actingAs($user)
            ->post(
                route('capers.create-basics'),
                [
                    'name' => $name,
                    'nav' => 'anchors',
                    'type' => 'caper',
                ]
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(
                config('app.url') . '/characters/capers/create/anchors'
            );
        $character->refresh();

        self::assertSame($name, $character->name);
        self::assertSame(Character::TYPE_CAPER, $character->type);
    }

    /**
     * Test trying to set the anchors for a character without sending any.
     * @test
     */
    public function testCreateAnchorsEmpty(): void
    {
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('capers.create-anchors'), [])
            ->assertSessionHasErrors(['identity', 'nav', 'vice', 'virtue']);
    }

    /**
     * Test trying to save a character's anchors.
     * @test
     */
    public function testCreateAnchors(): void
    {
        /** @var User */
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
        ]);
        session(['capers-partial' => $character->id]);

        self::assertNull($character->identity);
        $this->actingAs($user)
            ->post(
                route('capers.create-anchors'),
                [
                    'identity' => 'deviant',
                    'nav' => 'anchors',
                    'vice' => 'alcohol',
                    'virtue' => 'loyal',
                ]
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(
                config('app.url') . '/characters/capers/create/anchors'
            );
        $character->refresh();

        self::assertInstanceOf(Identity::class, $character->identity);
    }
}
