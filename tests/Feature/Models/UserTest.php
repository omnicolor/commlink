<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Character;
use App\Models\ChatUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

/**
 * Tests for the user class.
 * @group user
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
    public function setUp(): void
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
     * Test getting a user's characters if they have none.
     * @small
     * @test
     */
    public function testGetCharactersNone(): void
    {
        $user = User::factory()->create();
        self::assertEmpty($user->characters()->get());
    }

    /**
     * Test getting a user's characters if they have some.
     * @small
     * @test
     */
    public function testGetCharacters(): void
    {
        $user = User::factory()->create();
        $this->characters[] = Character::factory()
            ->create(['owner' => $user->email]);
        $this->characters[] = Character::factory()
            ->create(['owner' => $user->email]);
        self::assertSame(2, $user->characters()->count());
    }

    /**
     * Test getting a user's characters from a particular system.
     * @small
     * @test
     */
    public function testGetSystemCharacters(): void
    {
        $user = User::factory()->create();
        $this->characters[] = Character::factory()->create([
            'owner' => $user->email,
            'type' => 'shadowrun5e',
        ]);
        $this->characters[] = Character::factory()->create([
            'owner' => $user->email,
            'type' => 'cyberpunk2077',
        ]);
        self::assertSame(1, $user->characters('shadowrun5e')->count());
    }

    /**
     * Test getting a character's ChatUsers if they have none.
     * @small
     * @test
     */
    public function testGetChatUsersNone(): void
    {
        $user = User::factory()->create();
        self::assertEmpty($user->chatUsers);
    }

    /**
     * Test getting a character's ChatUsers.
     * @small
     * @test
     */
    public function testGetChatUsers(): void
    {
        $user = User::factory()->create();
        $chatUser = ChatUser::factory()->create(['user_id' => $user->id]);
        self::assertNotEmpty($user->chatUsers);
    }
}
