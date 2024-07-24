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
final class SkillsControllerTest extends TestCase
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
            ->getJson(route('alien.skills.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'attribute',
                        'id',
                        'name',
                        'page',
                        'ruleset',
                        'links',
                    ],
                ],
            ]);
    }

    public function testShow(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('alien.skills.show', 'heavy-machinery'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'attribute',
                    'id',
                    'name',
                    'page',
                    'ruleset',
                    'stunts',
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
            ->getJson(route('alien.skills.show', 'heavy-machinery'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'attribute',
                    'description',
                    'id',
                    'name',
                    'page',
                    'ruleset',
                    'stunts',
                    'links',
                ],
            ]);
    }
}
