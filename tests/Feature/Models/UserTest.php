<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Campaign;
use App\Models\Character;
use App\Models\ChatUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

/**
 * Tests for the user class.
 * @group campaigns
 * @group user
 * @medium
 */
final class UserTest extends \Tests\TestCase
{
    use RefreshDatabase;

    /**
     * Collection of characters creates during tests.
     * @var Collection
     */
    protected Collection $characters;

    /**
     * Set up a clean test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->characters = new Collection();
    }

    /**
     * Clean up after testing.
     */
    public function tearDown(): void
    {
        foreach ($this->characters as $key => $character) {
            $character->delete();
            unset($this->characters[$key]);
        }
        parent::tearDown();
    }

    /**
     * Test getting a user's campaigns if they have none.
     * @test
     */
    public function testCampaignsNone(): void
    {
        /** @var User */
        $user = User::factory()->create();
        self::assertCount(0, $user->campaigns);
        self::assertCount(0, $user->campaignsRegistered);
    }

    /**
     * Test getting a user's campaigns.
     * @test
     */
    public function testCampaigns(): void
    {
        /** @var User */
        $user = User::factory()->create();
        Campaign::factory()->create(['gm' => $user]);
        Campaign::factory()->create([
            'gm' => $user,
            'registered_by' => $user,
        ]);
        self::assertCount(2, $user->campaigns);
        self::assertCount(1, $user->campaignsRegistered);
    }

    /**
     * Test getting a user's characters if they have none.
     * @test
     */
    public function testGetCharactersNone(): void
    {
        /** @var User */
        $user = User::factory()->create();
        self::assertEmpty($user->characters()->get());
    }

    /**
     * Test getting a user's characters if they have some.
     * @test
     */
    public function testGetCharacters(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->characters[] = Character::factory()
            ->create(['owner' => $user->email]);
        $this->characters[] = Character::factory()
            ->create(['owner' => $user->email]);
        // @phpstan-ignore-next-line
        self::assertSame(2, $user->characters()->count());
    }

    /**
     * Test getting a user's characters from a particular system.
     * @test
     */
    public function testGetSystemCharacters(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->characters[] = Character::factory()->create([
            'owner' => $user->email,
            'type' => 'shadowrun5e',
        ]);
        $this->characters[] = Character::factory()->create([
            'owner' => $user->email,
            'type' => 'cyberpunk2077',
        ]);
        // @phpstan-ignore-next-line
        self::assertSame(1, $user->characters('shadowrun5e')->count());
    }

    /**
     * Test getting a character's ChatUsers if they have none.
     * @test
     */
    public function testGetChatUsersNone(): void
    {
        /** @var User */
        $user = User::factory()->create();
        self::assertEmpty($user->chatUsers);
    }

    /**
     * Test getting a character's ChatUsers.
     * @test
     */
    public function testGetChatUsers(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create(['user_id' => $user->id]);
        self::assertNotEmpty($user->chatUsers);
    }
}
