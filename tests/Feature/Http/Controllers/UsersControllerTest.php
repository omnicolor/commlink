<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Features\ChummerImport;
use App\Models\Campaign;
use App\Models\CampaignInvitation;
use App\Models\Channel;
use App\Models\Character;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\Deck;
use App\Models\Event;
use App\Models\EventRsvp;
use App\Models\Initiative;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Pennant\Feature;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tests for the users controller.
 * @group controllers
 * @group user
 * @medium
 */
final class UsersControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function createAdmin(): User
    {
        $user = User::factory()->create();
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::create(['name' => 'admin users']));
        $user->assignRole($admin);
        return $user;
    }

    protected function cleanDatabase(): void
    {
        DB::table('campaign_user')->truncate();
        Deck::truncate();
        Initiative::truncate();
        EventRsvp::truncate();
        Event::truncate();
        ChatCharacter::truncate();
        ChatUser::truncate();
        Channel::truncate();
        CampaignInvitation::truncate();
        Campaign::truncate();
        User::truncate();
    }

    /**
     * Test an authenticated request that's missing the admin role.
     * @test
     */
    public function testNonAdminIndex(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->get(route('users.index'))
            ->assertForbidden();
    }

    /**
     * Test that an admin can load the user admin page.
     * @large
     * @test
     */
    public function testAdminIndex(): void
    {
        $user = $this->createAdmin();
        self::actingAs($user)
            ->get(route('users.index'))
            ->assertOk()
            ->assertSee($user->email);
    }

    /**
     * Test loading the API index.
     * @large
     * @test
     */
    public function testIndexApi(): void
    {
        $user = User::factory()->hasCampaigns(1)->create();
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::create(['name' => 'admin users']));
        $user->assignRole($admin);
        $character = Character::factory()->create(['owner' => $user->email]);
        $campaign = Campaign::factory()->create(['gm' => $user->id]);

        self::actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->get(route('users.index'))
            ->assertJsonFragment(['id' => $character->id]);
    }

    /**
     * Test getting a user's information.
     * @test
     */
    public function testShowUser(): void
    {
        $user = $this->createAdmin();
        self::actingAs($user)
            ->get(route('users.show', ['user' => $user]))
            ->assertOk()
            ->assertJson(['data' => ['email' => $user->email]]);
    }

    /**
     * Test trying to patch a user with an invalid patch.
     * @test
     */
    public function testInvalidPatch(): void
    {
        $user = $this->createAdmin();
        $patch = [
            'patch' => [
                [
                    'invalid' => 'foo',
                ],
            ],
        ];
        self::actingAs($user)
            ->patchJson(route('users.update', ['user' => $user]), $patch)
            ->assertStatus(JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * Test trying to give a user a Feature that isn't found.
     * @test
     */
    public function testPatchForInvalidFeature(): void
    {
        $user = $this->createAdmin();
        $patch = [
            'patch' => [
                [
                    'path' => '/features/NotFound',
                    'op' => 'replace',
                    'value' => 'true',
                ],
            ],
        ];
        self::actingAs($user)
            ->patchJson(route('users.update', ['user' => $user]), $patch)
            ->assertNotFound();
    }

    /**
     * Test adding a feature to a user.
     * @test
     */
    public function testPatchUserAddFeature(): void
    {
        $user = $this->createAdmin();
        $patch = [
            'patch' => [
                [
                    'path' => '/features/ChummerImport',
                    'op' => 'replace',
                    'value' => 'true',
                ],
            ],
        ];

        self::actingAs($user)
            ->patchJson(route('users.update', ['user' => $user]), $patch)
            ->assertStatus(JsonResponse::HTTP_ACCEPTED)
            ->assertJson(['features' => ['ChummerImport']]);
    }

    /**
     * Test removing a feature from a user.
     * @test
     */
    public function testPatchUserRemoveFeature(): void
    {
        $user = $this->createAdmin();
        Feature::for($user)->activate(ChummerImport::class);
        $patch = [
            'patch' => [
                [
                    'path' => '/features/ChummerImport',
                    'op' => 'replace',
                    'value' => 'false',
                ],
            ],
        ];

        self::actingAs($user)
            ->patchJson(route('users.update', ['user' => $user]), $patch)
            ->assertStatus(JsonResponse::HTTP_ACCEPTED)
            ->assertJson(['features' => []]);

        $user->refresh();
        Feature::flushCache();
        self::assertCount(0, $user->getFeatures());
        self::assertFalse(Feature::for($user)->active(ChummerImport::class));
    }

    /**
     * Test trying to add an invalid role.
     * @test
     */
    public function testPatchAddInvalidRole(): void
    {
        $user = $this->createAdmin();
        $patch = [
            'patch' => [
                [
                    'path' => '/roles/NotFound',
                    'op' => 'replace',
                    'value' => 'true',
                ],
            ],
        ];

        self::actingAs($user)
            ->patchJson(route('users.update', ['user' => $user]), $patch)
            ->assertNotFound()
            ->assertJson(['error' => 'Invalid role']);
    }

    /**
     * Test giving a user a new role.
     * @test
     */
    public function testPatchAddRole(): void
    {
        $user = $this->createAdmin();
        $role = Role::create(['name' => 'trusted']);

        $patch = [
            'patch' => [
                [
                    // @phpstan-ignore-next-line
                    'path' => '/roles/' . $role->id,
                    'op' => 'replace',
                    'value' => 'true',
                ],
            ],
        ];

        self::actingAs($user)
            ->patchJson(route('users.update', ['user' => $user]), $patch)
            ->assertStatus(JsonResponse::HTTP_ACCEPTED)
            ->assertJson(['roles' => [
                ['name' => 'admin'],
                ['name' => 'trusted'],
            ]]);
        $user->refresh();
        self::assertCount(2, $user->roles);
    }

    /**
     * Test removing a role from a user.
     * @test
     */
    public function testPatchRemoveRole(): void
    {
        $user = $this->createAdmin();
        $role = Role::where('name', 'admin')->first();
        $patch = [
            'patch' => [
                [
                    // @phpstan-ignore-next-line
                    'path' => '/roles/' . $role->id,
                    'op' => 'replace',
                    'value' => 'false',
                ],
            ],
        ];

        self::actingAs($user)
            ->patchJson(route('users.update', ['user' => $user]), $patch)
            ->assertStatus(JsonResponse::HTTP_ACCEPTED)
            ->assertJson(['roles' => []]);
        $user->refresh();
        self::assertCount(0, $user->roles);
    }

    public function testCreateApiTokenForAnotherUser(): void
    {
        /** @var User */
        $innocentUser = User::factory()->create();
        /** @var User */
        $hacker = User::factory()->create();

        self::assertDatabaseMissing(
            'personal_access_tokens',
            ['name' => 'Hacked token']
        );
        self::actingAs($hacker)
            ->postJson(
                route('create-token', ['user' => $innocentUser]),
                ['name' => 'Hacked token'],
            )
            ->assertForbidden();
        self::assertDatabaseMissing(
            'personal_access_tokens',
            ['name' => 'Hacked token']
        );
    }

    public function testCreateApiTokenNoExpiration(): void
    {
        /** @var User */
        $user = User::factory()->create();

        $tokenName = Str::random(10);
        self::actingAs($user)
            ->postJson(
                route('create-token', ['user' => $user]),
                ['name' => $tokenName],
            )
            ->assertCreated();
        self::assertDatabaseHas(
            'personal_access_tokens',
            ['name' => $tokenName, 'expires_at' => null],
        );
    }

    public function testCreateApiTokenWithExpiration(): void
    {
        /** @var User */
        $user = User::factory()->create();

        $tokenName = Str::random(10);
        $expiration = CarbonImmutable::now()->addMonth();
        self::actingAs($user)
            ->postJson(
                route('create-token', ['user' => $user]),
                [
                    'name' => $tokenName,
                    'expires_at' => $expiration->toDateString(),
                ],
            )
            ->assertCreated();
        self::assertDatabaseHas(
            'personal_access_tokens',
            [
                'name' => $tokenName,
                'expires_at' => $expiration->startOfDay()->toDateTimeString(),
            ],
        );
    }

    public function testDeleteAnotherUsersToken(): void
    {
        /** @var User */
        $innocentUser = User::factory()->create();
        /** @var User */
        $hacker = User::factory()->create();

        $tokenName = Str::random(10);
        $token = $innocentUser->createToken($tokenName, ['*']);

        self::actingAs($hacker)
            ->delete(route(
                'delete-token',
                ['user' => $innocentUser, 'tokenId' => $token->accessToken->id],
            ))
            ->assertForbidden();
        self::assertDatabaseHas(
            'personal_access_tokens',
            [
                'id' => $token->accessToken->id,
                'name' => $tokenName,
            ],
        );
    }

    public function testDeleteToken(): void
    {
        /** @var User */
        $user = User::factory()->create();

        $tokenName = Str::random(10);
        $token = $user->createToken($tokenName, ['*']);

        self::actingAs($user)
            ->delete(route(
                'delete-token',
                ['user' => $user, 'tokenId' => $token->accessToken->id],
            ))
            ->assertNoContent();
        self::assertDatabaseMissing(
            'personal_access_tokens',
            [
                'id' => $token->accessToken->id,
                'name' => $tokenName,
            ],
        );
    }
}
