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
final class MetamagicsControllerTest extends TestCase
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
            ->getJson(route('shadowrun6e.metamagics.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'adept_only',
                        'id',
                        'name',
                        'page',
                        'ruleset',
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
            ->getJson(route('shadowrun6e.metamagics.show', 'centering'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'adept_only',
                    'description',
                    'id',
                    'name',
                    'page',
                    'ruleset',
                    'links',
                ],
            ]);
    }
}
