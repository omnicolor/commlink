<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Tests\Feature\Http\Controllers;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Override;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

use function route;

#[Group('shadowrun')]
#[Group('shadowrun6e')]
#[Medium]
final class QualitiesControllerTest extends TestCase
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
            ->getJson(route('shadowrun6e.qualities.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'effects',
                        'id',
                        'karma_cost',
                        'level',
                        'name',
                        'page',
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
            ->getJson(route('shadowrun6e.qualities.show', 'ambidextrous'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'description',
                    'effects',
                    'id',
                    'karma_cost',
                    'level',
                    'name',
                    'page',
                    'links',
                ],
            ]);
    }
}
