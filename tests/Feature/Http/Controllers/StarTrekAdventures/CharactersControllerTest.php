<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\StarTrekAdventures;

use App\Models\StarTrekAdventures\Character;
use App\Models\User;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('startrekadventures')]
#[Medium]
final class CharactersControllerTest extends TestCase
{
    /**
     * Test listing a user's Star Trek Adventures characters.
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
     */
    public function testListCharacters(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Character */
        $character = Character::factory()->create([
            'owner' => $user->email,
            'created_by' => self::class . '::' . __FUNCTION__,
        ]);

        $this->actingAs($user)
            ->get('/characters/star-trek-adventures')
            ->assertSee(e($character->name), false)
            ->assertSee('Star Trek Adventures Characters');

        $character->delete();
    }

    /**
     * Test loading a character view.
     */
    public function testViewCharacter(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Character */
        $character = Character::factory()->create([
            'owner' => $user->email,
            'created_by' => self::class . '::' . __FUNCTION__,
        ]);

        $this->actingAs($user)
            ->get(
                route('star-trek-adventures.character', $character),
                ['character' => $character]
            )
            ->assertSee($user->email)
            ->assertSee(e($character->name), false);

        $character->delete();
    }
}
