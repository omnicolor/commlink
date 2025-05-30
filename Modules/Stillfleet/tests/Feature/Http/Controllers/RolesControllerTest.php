<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Tests\Feature\Http\Controllers;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Override;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

use function route;

#[Group('stillfleet')]
#[Medium]
final class RolesControllerTest extends TestCase
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
            ->getJson(route('stillfleet.classes.index'))
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
            ->getJson(route('stillfleet.classes.show', 'banshee'))
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
            ->getJson(route('stillfleet.classes.show', 'banshee'))
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
