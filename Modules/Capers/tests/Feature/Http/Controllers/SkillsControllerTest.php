<?php

declare(strict_types=1);

namespace Modules\Capers\Tests\Feature\Http\Controllers;

use App\Models\User;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

#[Group('capers')]
#[Small]
final class SkillsControllerTest extends TestCase
{
    public function testIndex(): void
    {
        $trusted = Role::create(['name' => 'trusted']);
        $trusted->givePermissionTo(Permission::create(['name' => 'view data']));
        $user = User::factory()->create();
        $user->assignRole($trusted);

        self::actingAs($user)
            ->getJson(route('capers.skills.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'name',
                        'description',
                        'id',
                    ],
                ],
                'links' => [
                    'self',
                ],
            ]);
    }
}
