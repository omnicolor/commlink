<?php

declare(strict_types=1);

namespace Modules\Startrekadventures\Tests\Feature\Http\Controllers;

use App\Models\Campaign;
use App\Models\User;
use Modules\Startrekadventures\Models\Character;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

use function route;

#[Group('startrekadventures')]
#[Medium]
final class CharactersControllerTest extends TestCase
{
    public function testIndex(): void
    {
        $trusted = Role::create(['name' => 'trusted']);
        $trusted->givePermissionTo(Permission::create(['name' => 'view data']));
        $user = User::factory()->create();
        $user->assignRole($trusted);

        /** @var Character */
        $character1 = Character::factory()->create(['owner' => $user->email]);
        /** @var Character */
        $character2 = Character::factory()->create(['owner' => $user->email]);

        self::actingAs($user)
            ->getJson(route('startrekadventures.characters.index'))
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $character1->delete();
        $character2->delete();
    }

    public function testShowCharacter(): void
    {
        $trusted = Role::create(['name' => 'trusted']);
        $trusted->givePermissionTo(Permission::create(['name' => 'view data']));
        $user = User::factory()->create();
        $user->assignRole($trusted);
        /** @var Campaign */
        $campaign = Campaign::factory()->create(['system' => 'avatar']);
        /** @var Character */
        $character = Character::factory()->create([
            'campaign_id' => $campaign->id,
            'owner' => $user->email,
            'talents' => [['id' => 'bold-command']],
        ]);

        self::actingAs($user)
            ->getJson(route('startrekadventures.characters.show', $character))
            ->assertOk()
            ->assertJsonPath('data.name', $character->name)
            ->assertJsonPath('data.campaign_id', $campaign->id)
            ->assertJsonCount(1, 'data.talents');

        $character->delete();
    }

    /**
     * Test listing a user's Star Trek Adventures characters.
     */
    public function testListCharactersWithNone(): void
    {
        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)
            ->get(route('startrekadventures.characters.list'))
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

        self::actingAs($user)
            ->get(route('startrekadventures.characters.list'))
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

        self::actingAs($user)
            ->get(
                route('startrekadventures.character', $character),
                ['character' => $character]
            )
            ->assertSee($user->email)
            ->assertSee(e($character->name), false);

        $character->delete();
    }
}
