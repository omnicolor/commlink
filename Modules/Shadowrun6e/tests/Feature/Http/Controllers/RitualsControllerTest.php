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
final class RitualsControllerTest extends TestCase
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
            ->getJson(route('shadowrun6e.rituals.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'anchored',
                        'id',
                        'material_link',
                        'minion',
                        'name',
                        'page',
                        'ruleset',
                        'spell',
                        'spotter',
                        'threshold',
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
            ->getJson(route('shadowrun6e.rituals.show', 'circle-of-healing'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'anchored',
                    'description',
                    'id',
                    'material_link',
                    'minion',
                    'name',
                    'page',
                    'ruleset',
                    'spell',
                    'spotter',
                    'threshold',
                    'links',
                ],
            ]);
    }
}
