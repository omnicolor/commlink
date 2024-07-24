<?php

declare(strict_types=1);

namespace Modules\Alien\Tests\Feature\Http\Controllers;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

#[Group('alien')]
#[Medium]
final class WeaponsControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->app->make(PermissionRegistrar::class)
            ->forgetCachedPermissions();
    }

    public function testIndex(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('alien.weapons.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'bonus',
                        'class',
                        'cost',
                        'damage',
                        'id',
                        'modifiers',
                        'name',
                        'page',
                        'range',
                        'ruleset',
                        'weight',
                        'links',
                    ],
                ],
            ]);
    }

    public function testShow(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('alien.weapons.show', 'm4a3-service-pistol'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'bonus',
                    'class',
                    'cost',
                    'damage',
                    'id',
                    'modifiers',
                    'name',
                    'page',
                    'range',
                    'ruleset',
                    'weight',
                    'links',
                ],
            ])
            ->assertJsonMissingPath('data.description');
    }

    public function testShowTrusted(): void
    {
        $user = User::factory()->create();
        $user->assignRole('trusted');
        self::actingAs($user)
            ->getJson(route('alien.weapons.show', 'm4a3-service-pistol'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'bonus',
                    'class',
                    'cost',
                    'damage',
                    'description',
                    'id',
                    'modifiers',
                    'name',
                    'page',
                    'range',
                    'ruleset',
                    'weight',
                    'links',
                ],
            ]);
    }
}
