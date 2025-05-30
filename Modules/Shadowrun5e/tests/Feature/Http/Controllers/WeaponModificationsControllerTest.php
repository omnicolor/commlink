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
final class WeaponModificationsControllerTest extends TestCase
{
    public function testNoAuthIndex(): void
    {
        self::getJson(route('shadowrun5e.weapon-modifications.index'))
            ->assertUnauthorized();
    }

    public function testAuthIndex(): void
    {
        $trusted = Role::create(['name' => 'trusted']);
        $trusted->givePermissionTo(Permission::create(['name' => 'view data']));
        $user = User::factory()->create();

        $response = self::actingAs($user)
            ->getJson(route('shadowrun5e.weapon-modifications.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route('shadowrun5e.weapon-modifications.index'),
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    public function testNoAuthShow(): void
    {
        self::getJson(
            route('shadowrun5e.weapon-modifications.show', 'bayonet')
        )
            ->assertUnauthorized();
    }

    public function testNoAuthShowNotFound(): void
    {
        self::getJson(
            route('shadowrun5e.weapon-modifications.show', 'not-found')
        )
            ->assertUnauthorized();
    }

    public function testAuthShow(): void
    {
        $trusted = Role::create(['name' => 'trusted']);
        $trusted->givePermissionTo(Permission::create(['name' => 'view data']));
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.weapon-modifications.show', 'bayonet'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'availability' => '4R',
                    'cost' => 50,
                    'id' => 'bayonet',
                    'mount' => ['top', 'under'],
                    'name' => 'Bayonet',
                    'ruleset' => 'run-and-gun',
                    'type' => 'accessory',
                ],
            ]);
    }

    public function testAuthShowNotFound(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(
                route('shadowrun5e.weapon-modifications.show', 'not-found')
            )
            ->assertNotFound();
    }
}
