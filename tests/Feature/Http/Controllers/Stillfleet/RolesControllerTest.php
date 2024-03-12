<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Stillfleet;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * @group controllers
 * @group stillfleet
 * @medium
 */
final class RolesControllerTest extends TestCase
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
            ->getJson(route('stillfleet.roles.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                    'grit',
                    'id',
                    'name',
                    'page',
                    'advanced_power_lists',
                    'marquee_power' => [
                        'id',
                        'name',
                        'page',
                        'ruleset',
                        'type',
                        'links',
                    ],
                    'optional_powers' => [
                        '*' => [
                            'id',
                            'name',
                            'page',
                            'ruleset',
                            'type',
                            'links',
                        ],
                    ],
                    'responsibilities',
                    'ruleset',
                    'links',
                    ],
                ],
            ]);
    }

    public function testShow(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('stillfleet.roles.show', 'banshee'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'grit',
                    'id',
                    'name',
                    'page',
                    'advanced_power_lists',
                    'marquee_power' => [
                        'id',
                        'name',
                        'page',
                        'ruleset',
                        'type',
                        'links',
                    ],
                    'optional_powers' => [
                        '*' => [
                            'id',
                            'name',
                            'page',
                            'ruleset',
                            'type',
                            'links',
                        ],
                    ],
                    'responsibilities',
                    'ruleset',
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
            ->getJson(route('stillfleet.roles.show', 'banshee'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'description',
                    'grit',
                    'id',
                    'name',
                    'page',
                    'advanced_power_lists',
                    'marquee_power' => [
                        'id',
                        'name',
                        'page',
                        'ruleset',
                        'type',
                        'links',
                    ],
                    'optional_powers' => [
                        '*' => [
                            'id',
                            'name',
                            'page',
                            'ruleset',
                            'type',
                            'links',
                        ],
                    ],
                    'responsibilities',
                    'ruleset',
                    'links',
                ],
            ]);
    }
}
