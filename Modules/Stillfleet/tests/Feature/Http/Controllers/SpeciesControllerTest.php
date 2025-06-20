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
final class SpeciesControllerTest extends TestCase
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
            ->getJson(route('stillfleet.species.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'page',
                        'powers',
                        'optional_powers',
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
            ->getJson(route('stillfleet.species.show', 'fleeter'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'description',
                    'id',
                    'name',
                    'page',
                    'powers',
                    'optional_powers',
                    'ruleset',
                    'links',
                ],
            ]);
    }
}
