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
final class SkillsControllerTest extends TestCase
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
            ->getJson(route('cyberpunkred.skills.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'attribute',
                        'category',
                        'name',
                        'page',
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
            ->getJson(route('cyberpunkred.skills.show', 'not-found'))
            ->assertNotFound();
    }

    public function testShow(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('cyberpunkred.skills.show', 'business'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'attribute',
                    'category',
                    'name',
                    'page',
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
            ->getJson(route('cyberpunkred.skills.show', 'concentration'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'attribute',
                    'category',
                    'description',
                    'examples',
                    'name',
                    'page',
                    'links' => [
                        'self',
                    ],
                ],
            ]);
    }
}
