<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Http\Controllers;

use App\Models\User;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

use function count;
use function route;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Medium]
final class WeaponsControllerTest extends TestCase
{
    public function testNoAuthIndex(): void
    {
        self::getJson(route('shadowrun5e.weapons.index'))
            ->assertUnauthorized();
    }

    public function testAuthIndex(): void
    {
        $trusted = Role::create(['name' => 'trusted']);
        $trusted->givePermissionTo(Permission::create(['name' => 'view data']));
        $user = User::factory()->create();
        $user->assignRole($trusted);
        $response = self::actingAs($user)
            ->getJson(route('shadowrun5e.weapons.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route('shadowrun5e.weapons.show', 'ak-98'),
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    public function testNoAuthShowNotFound(): void
    {
        self::getJson(route('shadowrun5e.weapons.show', 'not-found'))
            ->assertUnauthorized();
    }

    public function testAuthShow(): void
    {
        $trusted = Role::create(['name' => 'trusted']);
        $trusted->givePermissionTo(Permission::create(['name' => 'view data']));
        $user = User::factory()->create();
        $user->assignRole($trusted);
        self::actingAs($user)
            ->getJson(route('shadowrun5e.weapons.show', 'ak-98'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'accuracy' => '5',
                    'armor_piercing' => -2,
                    'availability' => '8F',
                    'class' => 'Assault rifle',
                    'cost' => 1250,
                    'damage' => '10P',
                    'description' => 'AK-98 description.',
                    'id' => 'ak-98',
                    'mounts' => [
                        'top' => null,
                        'barrel' => null,
                        'stock' => null,
                    ],
                    'name' => 'AK-98',
                    'page' => null,
                    'ruleset' => 'run-and-gun',
                    'skill' => 'automatics',
                    'type' => 'firearm',
                    'links' => ['self' => route('shadowrun5e.weapons.show', 'ak-98')],
                    'ammo_capacity' => 38,
                    'ammo_container' => 'c',
                    'range' => '25/150/350/550',
                    'firing_modes' => ['SA', 'BF', 'FA'],
                ],
            ]);
    }
}
