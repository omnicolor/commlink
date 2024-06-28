<?php

declare(strict_types=1);

namespace Modules\Avatar\Tests\Feature\Http\Controllers;

use App\Models\User;
use Modules\Avatar\Models\Character;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('avatar')]
#[Medium]
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
        $character = Character::factory()->create(['owner' => $user->email]);

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
