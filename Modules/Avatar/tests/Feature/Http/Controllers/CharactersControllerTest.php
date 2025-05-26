<?php

declare(strict_types=1);

namespace Modules\Avatar\Tests\Feature\Http\Controllers;

use App\Models\Campaign;
use App\Models\User;
use Modules\Avatar\Models\Character;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

#[Group('avatar')]
#[Medium]
final class CharactersControllerTest extends TestCase
{
    public function testIndex(): void
    {
        $trusted = Role::create(['name' => 'trusted']);
        $trusted->givePermissionTo(Permission::create(['name' => 'view data']));
        $user = User::factory()->create();
        $user->assignRole($trusted);

        $character1 = Character::factory()->create([
            'owner' => $user->email,
            'playbook' => 'the-adamant',
        ]);
        $character2 = Character::factory()->create([
            'owner' => $user->email,
            'playbook' => 'the-adamant',
        ]);

        self::actingAs($user)
            ->getJson(route('avatar.characters.index'))
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

        $campaign = Campaign::factory()->create(['system' => 'avatar']);
        $character = Character::factory()->create([
            'campaign_id' => $campaign->id,
            'owner' => $user->email,
            'playbook' => 'the-adamant',
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
        $character = Character::factory()->create([
            'owner' => $user->email,
            'playbook' => 'the-adamant',
        ]);

        $this->actingAs($user)
            ->get(route('avatar.character', $character))
            ->assertSee($user->email->address)
            ->assertSee(e($character->name), false);
        $character->delete();
    }
}
