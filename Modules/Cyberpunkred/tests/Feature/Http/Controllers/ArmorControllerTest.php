<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Tests\Feature\Http\Controllers;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Override;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

#[Group('cyberpunkred')]
#[Medium]
final class ArmorControllerTest extends TestCase
{
    #[Override]
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
            ->getJson(route('cyberpunkred.armor.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'type',
                        'cost_category',
                        'page',
                        'penalty',
                        'ruleset',
                        'stopping_power',
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
            ->getJson(route('cyberpunkred.armor.show', 'not-found'))
            ->assertNotFound();
    }

    public function testShow(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('cyberpunkred.armor.show', 'leathers'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'type',
                    'cost_category',
                    'page',
                    'penalty',
                    'ruleset',
                    'stopping_power',
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
            ->getJson(route('cyberpunkred.armor.show', 'leathers'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'type',
                    'cost_category',
                    'description',
                    'page',
                    'penalty',
                    'ruleset',
                    'stopping_power',
                    'links' => [
                        'self',
                    ],
                ],
            ]);
    }
}
