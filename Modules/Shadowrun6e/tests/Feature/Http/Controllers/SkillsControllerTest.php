<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Tests\Feature\Http\Controllers;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

use function route;

#[Group('shadowrun')]
#[Group('shadowrun6e')]
#[Medium]
class SkillsControllerTest extends TestCase
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
            ->getJson(route('shadowrun6e.skills.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'attribute',
                        'attributes_secondary',
                        'example_specializations',
                        'id',
                        'name',
                        'page',
                        'untrained',
                        'links',
                    ],
                ],
            ]);
    }


    public function testShowTrusted(): void
    {
        $user = User::factory()->create();
        $user->assignRole('trusted');
        self::actingAs($user)
            ->getJson(route('shadowrun6e.skills.show', 'astral'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'attribute',
                    'attributes_secondary',
                    'description',
                    'example_specializations',
                    'id',
                    'name',
                    'page',
                    'untrained',
                    'links',
                ],
            ]);
    }
}
