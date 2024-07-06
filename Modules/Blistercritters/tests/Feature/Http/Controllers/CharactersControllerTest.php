<?php

declare(strict_types=1);

namespace Modules\Blistercritters\Tests\Feature\Http\Controllers;

use App\Models\Campaign;
use App\Models\User;
use Modules\Blistercritters\Models\Character;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('blistercritters')]
#[Medium]
final class CharactersControllerTest extends TestCase
{
    public function testIndex(): void
    {
        $user = User::factory()->create();
        $character = Character::factory()->create(['owner' => $user->email]);
        self::actingAs($user)
            ->getJson(route('blistercritters.characters.index'))
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $character->delete();
    }

    public function testShowAnothersCharacter(): void
    {
        $user = User::factory()->create();
        $character = Character::factory()->create();
        self::actingAs($user)
            ->getJson(route('blistercritters.characters.show', $character))
            ->assertNotFound();

        $character->delete();
    }

    public function testShowCharacter(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'system' => 'blistercritters',
        ]);
        $character = Character::factory()->create([
            'campaign_id' => $campaign->id,
            'name' => 'Roa Dent',
            'owner' => $user->email,
        ]);
        self::actingAs($user)
            ->getJson(route('blistercritters.characters.show', $character))
            ->assertOk()
            ->assertJsonPath('data.name', 'Roa Dent')
            ->assertJsonPath('data.campaign_id', $campaign->id);

        $character->delete();
    }

    public function testViewCharacter(): void
    {
        $user = User::factory()->create();

        /** @var Character */
        $character = Character::factory()->create(['owner' => $user->email]);

        self::actingAs($user)
            ->get(route('blistercritters.character', $character))
            ->assertSee($user->email)
            ->assertSee(e($character->name), false);

        $character->delete();
    }
}
