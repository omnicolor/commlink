<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Subversion;

use App\Models\Subversion\Character;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * @group subversion
 * @medium
 */
final class CharactersControllerTest extends TestCase
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
        $user = User::factory()->create();
        $character = Character::factory()->create(['owner' => $user->email]);
        self::actingAs($user)
            ->getJson(route('subversion.characters.index'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', $character->name);
    }

    public function testShow(): void
    {
        $user = User::factory()->create();
        $character = Character::factory()->create(['owner' => $user->email]);
        self::actingAs($user)
            ->getJson(route('subversion.characters.show', $character))
            ->assertOk()
            ->assertJsonPath('data.name', $character->name);
    }
}
