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
final class PlaybooksControllerTest extends TestCase
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
            ->getJson(route('avatar.playbooks.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'advanced_technique',
                        'balance_left',
                        'balance_right',
                        'base_stats' => [
                            'creativity',
                            'focus',
                            'harmony',
                            'passion',
                        ],
                        'demeanor_options',
                        'history',
                        'id',
                        'moment_of_balance',
                        'moves',
                        'name',
                        'page',
                        'ruleset',
                        'links',
                    ],
                ],
            ]);
    }

    public function testShow(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('avatar.playbooks.show', 'the-adamant'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'advanced_technique',
                    'balance_left',
                    'balance_right',
                    'base_stats' => [
                        'creativity',
                        'focus',
                        'harmony',
                        'passion',
                    ],
                    'demeanor_options',
                    'history',
                    'id',
                    'moment_of_balance',
                    'moves',
                    'name',
                    'page',
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
            ->getJson(route('avatar.playbooks.show', 'the-adamant'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'advanced_technique',
                    'balance_left',
                    'balance_right',
                    'base_stats' => [
                        'creativity',
                        'focus',
                        'harmony',
                        'passion',
                    ],
                    'demeanor_options',
                    'description',
                    'history',
                    'id',
                    'moment_of_balance',
                    'moves',
                    'name',
                    'page',
                    'ruleset',
                    'links',
                ],
            ]);
    }
}
