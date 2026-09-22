<?php

declare(strict_types=1);

namespace Modules\Battletech\Tests\Feature\Http\Controllers;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Override;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

#[Group('battletech')]
#[Medium]
final class TraitsControllerTest extends TestCase
{
    private User $user;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        if (isset($this->user)) {
            return;
        }
        $this->seed(RoleAndPermissionSeeder::class);
        $this->app->make(PermissionRegistrar::class)
            ->forgetCachedPermissions();
        $this->user = User::factory()->create();
        $this->user->assignRole('trusted');
    }

    public function testIndex(): void
    {
        self::actingAs($this->user)
            ->getJson(route('battletech.traits.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'cost',
                        'description',
                        'id',
                        'name',
                        'opposes',
                        'page',
                        'quote',
                        'ruleset',
                        'types',
                        'links',
                    ],
                ],
            ]);
    }

    public function testShow(): void
    {
        self::actingAs($this->user)
            ->getJson(route('battletech.traits.show', 'ambidextrous'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'cost',
                    'description',
                    'id',
                    'name',
                    'opposes',
                    'page',
                    'quote',
                    'ruleset',
                    'types',
                    'links',
                ],
            ]);
    }
}
