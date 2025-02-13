<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\Character;
use App\Models\User;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

use function route;

#[Small]
final class CharactersControllerTest extends TestCase
{
    public function testExample(): void
    {
        $user = User::factory()->create();

        $character1 = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'alien',
        ]);
        $character2 = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'unknown',
        ]);

        self::actingAs($user)
            ->getJson(route('characters.index'))
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['links' => [
                'json' => route('alien.characters.show', $character1),
                'html' => route('alien.character', $character1),
            ]])
            ->assertJsonFragment(['links' => ['html' => null, 'json' => null]]);

        $character1->delete();
        $character2->delete();
    }
}
