<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Avatar;

use App\Models\Avatar\Character;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @group avatar
 * @group controllers
 * @medium
 */
final class CharactersControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test loading a character view.
     * @test
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
