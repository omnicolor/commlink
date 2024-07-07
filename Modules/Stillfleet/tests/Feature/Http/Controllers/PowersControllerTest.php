<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Tests\Feature\Http\Controllers;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

#[Group('stillfleet')]
#[Medium]
final class PowersControllerTest extends TestCase
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
            ->getJson(route('stillfleet.powers.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'page',
                        'ruleset',
                        'type',
                        'links',
                    ],
                ],
            ]);
    }

    public function testShow(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('stillfleet.powers.show', 'ally'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'advanced_list',
                    'id',
                    'name',
                    'page',
                    'ruleset',
                    'type',
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
            ->getJson(route('stillfleet.powers.show', 'ally'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'advanced_list',
                    'description',
                    'id',
                    'name',
                    'page',
                    'ruleset',
                    'type',
                    'links',
                ],
            ]);
    }
}
