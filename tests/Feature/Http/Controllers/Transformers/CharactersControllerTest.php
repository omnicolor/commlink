<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Transformers;

use App\Models\Transformers\Character;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @group transformers
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
                route('transformers.character', $character),
                ['character' => $character, 'user' => $user]
            )
            ->assertSee($user->email)
            ->assertSee(e($character->name), false);
        $character->delete();
    }
}
