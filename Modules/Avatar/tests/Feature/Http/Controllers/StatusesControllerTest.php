<?php

declare(strict_types=1);

namespace Modules\Avatar\Tests\Feature\Http\Controllers;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

#[Group('avatar')]
#[Medium]
final class StatusesControllerTest extends TestCase
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
            ->getJson(route('avatar.statuses.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'effect',
                        'id',
                        'name',
                        'page',
                        'ruleset',
                        'short_description',
                        'links',
                    ],
                ],
            ]);
    }

    public function testShow(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('avatar.statuses.show', 'doomed'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'effect',
                    'id',
                    'name',
                    'page',
                    'ruleset',
                    'short_description',
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
            ->getJson(route('avatar.statuses.show', 'doomed'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'description',
                    'effect',
                    'id',
                    'name',
                    'page',
                    'ruleset',
                    'short_description',
                    'links',
                ],
            ]);
    }
}
