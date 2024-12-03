<?php

declare(strict_types=1);

namespace Modules\Root\Tests\Feature\Http\Controllers;

use App\Models\Campaign;
use App\Models\User;
use Modules\Root\Models\Character;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

#[Group('root')]
#[Medium]
final class CharactersControllerTest extends TestCase
{
    public function testIndex(): void
    {
        $trusted = Role::create(['name' => 'trusted']);
        $trusted->givePermissionTo(Permission::create(['name' => 'view data']));
        $user = User::factory()->create();
        $user->assignRole($trusted);

        $character1 = Character::factory()->create(['owner' => $user->email]);
        $character2 = Character::factory()->create(['owner' => $user->email]);

        self::actingAs($user)
            ->getJson(route('root.characters.index'))
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

        $campaign = Campaign::factory()->create(['system' => 'alien']);
        $character = Character::factory()->create([
            'campaign_id' => $campaign,
            'owner' => $user->email,
        ]);

        self::actingAs($user)
            ->getJson(route('root.characters.show', $character))
            ->assertOk()
            ->assertJsonPath('data.name', $character->name)
            ->assertJsonPath('data.campaign_id', $campaign->id);

        $character->delete();
    }

    public function testViewCharacter(): void
    {
        $trusted = Role::create(['name' => 'trusted']);
        $trusted->givePermissionTo(Permission::create(['name' => 'view data']));
        $user = User::factory()->create();
        $user->assignRole($trusted);

        $character = Character::factory()->create([
            'moves' => ['brute', 'cleave'],
            'name' => 'Trash Panda',
            'nature' => 'punisher',
            'owner' => $user->email,
            'playbook' => 'arbiter',
        ]);

        self::actingAs($user)
            ->get(route('root.character', $character))
            ->assertOk()
            ->assertSee('Trash Panda, The Arbiter');
        $character->delete();
    }
}
