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
final class RacesControllerTest extends TestCase
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
            ->getJson(route('shadowrun6e.races.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'agility' => ['min', 'max'],
                        'body' => ['min', 'max'],
                        'charisma' => ['min', 'max'],
                        'dermal_armor',
                        'edge' => ['min', 'max'],
                        'id',
                        'intuition' => ['min', 'max'],
                        'logic' => ['min', 'max'],
                        'name',
                        'page',
                        'reach',
                        'reaction' => ['min', 'max'],
                        'ruleset',
                        'special_points',
                        'strength' => ['min', 'max'],
                        'willpower' => ['min', 'max'],
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
            ->getJson(route('shadowrun6e.races.show', 'dwarf'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'agility' => ['min', 'max'],
                    'body' => ['min', 'max'],
                    'charisma' => ['min', 'max'],
                    'dermal_armor',
                    'description',
                    'edge' => ['min', 'max'],
                    'id',
                    'intuition' => ['min', 'max'],
                    'logic' => ['min', 'max'],
                    'name',
                    'page',
                    'reach',
                    'reaction' => ['min', 'max'],
                    'ruleset',
                    'special_points' => [
                        'A',
                        'B',
                        'C',
                        'D',
                        'E',
                    ],
                    'strength' => ['min', 'max'],
                    'willpower' => ['min', 'max'],
                    'links',
                ],
            ]);
    }
}
