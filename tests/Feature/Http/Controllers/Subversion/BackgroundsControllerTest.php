<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Subversion;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * @group subversion
 * @medium
 */
final class BackgroundsControllerTest extends TestCase
{
    use RefreshDatabase;

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
            ->getJson(route('subversion.backgrounds.index'))
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function testShow(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('subversion.backgrounds.show', 'agriculturist'))
            ->assertOk()
            ->assertJsonPath('data.name', 'Agriculturist');
    }
}
