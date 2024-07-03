<?php

declare(strict_types=1);

namespace Modules\Avatar\Tests\Feature\Http\Controllers;

use App\Models\Campaign;
use App\Models\User;
use Modules\Avatar\Models\Character;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('avatar')]
#[Medium]
final class CharactersControllerTest extends TestCase
{
    public function testIndex(): void
    {
        $user = User::factory()->create();

        /** @var Character */
        $character1 = Character::factory()->create(['owner' => $user->email]);
        /** @var Character */
        $character2 = Character::factory()->create(['owner' => $user->email]);

        self::actingAs($user)
            ->getJson(route('avatar.characters.index'))
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $character1->delete();
        $character2->delete();
    }

    public function testShowCharacter(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()->create(['system' => 'avatar']);
        /** @var Character */
        $character = Character::factory()->create([
            'campaign_id' => $campaign,
            'owner' => $user->email,
        ]);

        self::actingAs($user)
            ->getJson(route('avatar.characters.show', $character))
            ->assertOk()
            ->assertJsonPath('data.name', $character->name)
            ->assertJsonPath('data.campaign_id', $campaign->id);

        $character->delete();
    }

    public function testViewCharacter(): void
    {
        $user = User::factory()->create();

        /** @var Character */
        $character = Character::factory()->create(['owner' => $user->email]);

        $this->actingAs($user)
            ->get(route('avatar.character', $character))
            ->assertSee($user->email)
            ->assertSee(e($character->name), false);
        $character->delete();
    }
}
