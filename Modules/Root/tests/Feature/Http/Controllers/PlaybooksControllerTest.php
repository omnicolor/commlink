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
final class PlaybooksControllerTest extends TestCase
{
    public function testIndex(): void
    {
        $trusted = Role::create(['name' => 'trusted']);
        $trusted->givePermissionTo(Permission::create(['name' => 'view data']));
        $user = User::factory()->create();
        $user->assignRole($trusted);
        self::actingAs($user)
            ->getJson(route('root.playbooks.index'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'The Arbiter');
    }

    public function testShow(): void
    {
        $trusted = Role::create(['name' => 'trusted']);
        $trusted->givePermissionTo(Permission::create(['name' => 'view data']));
        $user = User::factory()->create();
        $user->assignRole($trusted);
        self::actingAs($user)
            ->getJson(route('root.playbooks.show', 'arbiter'))
            ->assertOk()
            ->assertJsonPath('data.stats.might', 2);
    }
}
