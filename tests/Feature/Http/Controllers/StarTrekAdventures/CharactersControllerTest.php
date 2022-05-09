<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\StarTrekAdventures;

use App\Models\StarTrekAdventures\Character;
use App\Models\User;
use Tests\TestCase;

/**
 * @group star-trek-adventures
 * @group controllers
 * @medium
 */
final class CharactersControllerTest extends TestCase
{
    /**
     * Test listing a user's Star Trek Adventures characters.
     * @test
     */
    public function testListCharactersWithNone(): void
    {
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/characters/star-trek-adventures')
            ->assertSee('You don\'t have any characters!', false)
            ->assertSee('Star Trek Adventures Characters');
    }

    /**
     * Test listing a user's Star Trek Adventures characters.
     * @test
     */
    public function testListCharacters(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Character */
        $character = Character::factory()->create(['owner' => $user->email]);

        $view = $this->actingAs($user)
            ->get('/characters/star-trek-adventures')
            ->assertSee(e($character->name), false)
            ->assertSee('Star Trek Adventures Characters');
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
                route('star-trek-adventures.character', $character),
                ['character' => $character]
            )
            ->assertSee($user->email)
            ->assertSee(e($character->name), false);
    }
}
