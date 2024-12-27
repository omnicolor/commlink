<?php

declare(strict_types=1);

namespace Modules\Root\Tests\Feature\Http\Controllers;

use App\Models\User;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

#[Group('root')]
#[Medium]
final class MovesControllerTest extends TestCase
{
    public function testIndex(): void
    {
        $trusted = Role::create(['name' => 'trusted']);
        $trusted->givePermissionTo(Permission::create(['name' => 'view data']));
        $user = User::factory()->create();
        $user->assignRole($trusted);
        self::actingAs($user)
            ->getJson(route('root.moves.index'))
            ->assertOk()
            ->assertJsonCount(12, 'data')
            ->assertJsonPath('data.1.name', 'Brute');
    }

    public function testShow(): void
    {
        $trusted = Role::create(['name' => 'trusted']);
        $trusted->givePermissionTo(Permission::create(['name' => 'view data']));
        $user = User::factory()->create();
        $user->assignRole($trusted);
        self::actingAs($user)
            ->getJson(route('root.moves.show', 'brute'))
            ->assertOk()
            ->assertJsonPath('data.effects.might', 1);
    }
}
