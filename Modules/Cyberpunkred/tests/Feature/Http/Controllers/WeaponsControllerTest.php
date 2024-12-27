<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Tests\Feature\Http\Controllers;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

#[Group('cyberpunkred')]
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
            ->getJson(route('cyberpunkred.weapons.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'type',
                        'class',
                        'concealable',
                        'cost',
                        'damage',
                        'examples' => [
                            'poor',
                            'standard',
                            'excellent',
                        ],
                        'hands_required',
                        'skill',
                        'links' => [
                            'self',
                        ],
                    ],
                ],
            ]);
    }

    public function testShowNotFound(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('cyberpunkred.weapons.show', 'not-found'))
            ->assertNotFound();
    }

    public function testShow(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('cyberpunkred.weapons.show', 'medium-pistol'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'type',
                    'class',
                    'concealable',
                    'cost',
                    'damage',
                    'examples' => [
                        'poor',
                        'standard',
                        'excellent',
                    ],
                    'hands_required',
                    'magazine',
                    'skill',
                    'links' => [
                        'self',
                    ],
                ],
            ])
            ->assertJsonMissingPath('data.description');
    }

    public function testShowTrusted(): void
    {
        $user = User::factory()->create();
        $user->assignRole('trusted');
        self::actingAs($user)
            ->getJson(route('cyberpunkred.weapons.show', 'medium-melee'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'type',
                    'class',
                    'concealable',
                    'cost',
                    'damage',
                    'examples' => [
                        'poor',
                        'standard',
                        'excellent',
                    ],
                    'hands_required',
                    'skill',
                    'links' => [
                        'self',
                    ],
                ],
            ]);
    }
}
