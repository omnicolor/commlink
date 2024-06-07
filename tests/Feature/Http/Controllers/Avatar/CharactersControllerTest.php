<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Avatar;

use App\Models\Avatar\Character;
use App\Models\User;
use Tests\TestCase;

/**
 * @group avatar
 * @medium
 */
final class CharactersControllerTest extends TestCase
{
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
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        $this->actingAs($user)
            ->get(
                route('avatar.character', $character),
                ['character' => $character, 'user' => $user]
            )
            ->assertSee($user->email)
            ->assertSee(e($character->name), false);
        $character->delete();
    }
}
