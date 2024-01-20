<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Transformers;

use App\Models\Transformers\Character;
use App\Models\Transformers\PartialCharacter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function sprintf;

/**
 * @group transformers
 * @medium
 */
final class CharactersControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateNewCharacter(): void
    {
        session(['transformers-partial' => 'old-character-id']);
        $user = User::factory()->create();
        self::assertCount(
            0,
            PartialCharacter::where('owner', $user->email)->get(),
        );
        self::actingAs($user)
            ->get('/characters/transformers/create/new')
            ->assertRedirect(
                config('app.url') . '/characters/transformers/create/base'
            );
        self::assertNotSame('old-character-id', session('transformers-partial'));
        self::assertCount(
            1,
            PartialCharacter::where('owner', $user->email)->get(),
        );
    }

    public function testCreateWithoutPartialCharacter(): void
    {
        $user = User::factory()->create();
        self::assertNull(session('transformers-partial'));
        self::assertCount(
            0,
            PartialCharacter::where('owner', $user->email)->get(),
        );
        self::actingAs($user)
            ->get('/characters/transformers/create')
            ->assertOk()
            ->assertSee('What do other transformers know you as?');
        self::assertNotNull(session('transformers-partial'));
        self::assertCount(
            1,
            PartialCharacter::where('owner', $user->email)->get(),
        );
    }

    public function testCreateWithPartialCharacterInUrl(): void
    {
        $user = User::factory()->create();
        /** @var PartialCharacter */
        $character = PartialCharacter::factory()
            ->create(['owner' => $user->email]);
        self::actingAs($user)
            ->get(sprintf('/characters/transformers/create/%s', $character->id))
            ->assertRedirect(
                config('app.url') . '/characters/transformers/create/base'
            );
    }

    public function testCreateWithPartialCharacterInSession(): void
    {
        $user = User::factory()->create();
        /** @var PartialCharacter */
        $character = PartialCharacter::factory()
            ->create(['owner' => $user->email]);
        session(['transformers-partial' => $character->id]);
        self::actingAs($user)
            ->get('/characters/transformers/create')
            ->assertOk()
            ->assertSee('What do other transformers know you as?');
    }

    public function testCreateTryAltModeBeforeStats(): void
    {
        $user = User::factory()->create();
        /** @var PartialCharacter */
        $character = PartialCharacter::factory()
            ->create(['owner' => $user->email]);
        session(['transformers-partial' => $character->id]);
        self::actingAs($user)
            ->get('/characters/transformers/create/alt-mode')
            ->assertRedirect(
                config('app.url') . '/characters/transformers/create/statistics'
            );
    }

    public function testViewCharacter(): void
    {
        $user = User::factory()->create();

        /** @var Character */
        $character = Character::factory()->create(['owner' => $user->email]);

        self::actingAs($user)
            ->get(
                route('transformers.character', $character),
                ['character' => $character]
            )
            ->assertSee($user->email)
            ->assertSee(e($character->name), false);
        $character->delete();
    }
}
