<?php

declare(strict_types=1);

namespace Modules\Subversion\Tests\Feature\Http\Controllers;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Override;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

use function route;

#[Group('subversion')]
#[Medium]
final class CastesControllerTest extends TestCase
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
            ->getJson(route('subversion.castes.index'))
            ->assertOk()
            ->assertJsonCount(6, 'data');
    }

    public function testShow(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('subversion.castes.show', 'lower'))
            ->assertOk()
            ->assertJsonPath('data.name', 'Lower caste');
    }
}
