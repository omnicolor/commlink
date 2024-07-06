<?php

declare(strict_types=1);

namespace Modules\Subversion\Tests\Feature\Http\Controllers;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

#[Group('subversion')]
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
