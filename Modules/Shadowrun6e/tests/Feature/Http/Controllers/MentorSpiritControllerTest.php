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
final class MentorSpiritControllerTest extends TestCase
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
            ->getJson(route('shadowrun6e.mentor-spirits.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'advantages' => [
                            'all',
                            'magician',
                            'adept',
                        ],
                        'disadvantages',
                        'id',
                        'name',
                        'page',
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
            ->getJson(route('shadowrun6e.mentor-spirits.show', 'bear'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'advantages' => [
                        'all',
                        'magician',
                        'adept',
                    ],
                    'description',
                    'disadvantages',
                    'id',
                    'name',
                    'page',
                    'ruleset',
                    'links',
                ],
            ]);
    }
}
