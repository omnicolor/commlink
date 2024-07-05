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
final class PowersControllerTest extends TestCase
{
    public function testIndex(): void
    {
        $trusted = Role::create(['name' => 'trusted']);
        $trusted->givePermissionTo(Permission::create(['name' => 'view data']));
        $user = User::factory()->create();
        $user->assignRole($trusted);

        self::actingAs($user)
            ->getJson(route('capers.powers.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'name',
                        'activation',
                        'available_boosts',
                        'boosts',
                        'description',
                        'duration',
                        'effect',
                        'id',
                        'max_rank',
                        'range',
                        'target',
                        'type',
                        'rank',
                        'links' => [
                            'self',
                        ],
                    ],
                ],
                'links' => [
                    'self',
                ],
            ]);
    }

    public function testShowNotFound(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('capers.powers.show', 'unknown'))
            ->assertNotFound();
    }

    public function testShow(): void
    {
        $trusted = Role::create(['name' => 'trusted']);
        $trusted->givePermissionTo(Permission::create(['name' => 'view data']));

        // User doesn't get the permission.
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('capers.powers.show', 'acid-stream'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'name',
                    'activation',
                    'available_boosts',
                    'boosts',
                    'duration',
                    'id',
                    'max_rank',
                    'range',
                    'target',
                    'type',
                    'rank',
                    'links' => [
                        'self',
                    ],
                ],
                'links' => [
                    'self',
                    'collection',
                ],
            ])
            ->assertJsonMissingPath('data.description')
            ->assertJsonMissingPath('data.effect');
    }
}
