<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Subversion;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * @group subversion
 * @medium
 */
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
            ->getJson(route('subversion.skills.index'))
            ->assertOk()
            ->assertJsonCount(12, 'data');
    }

    public function testShow(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('subversion.skills.show', 'observation'))
            ->assertOk()
            ->assertJsonPath('data.name', 'Observation');
    }
}
