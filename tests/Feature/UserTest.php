<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Character;
use App\Models\SlackLink;
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

    /** * Set up a clean test environment.
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
     * @test
     */
    public function testGetCharactersNone(): void
    {
        $user = User::factory()->create();
        self::assertEmpty($user->characters()->get());
    }

    /**
     * Test getting a user's characters if they have some.
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
     * Test getting the SlackLinks for a user if they have none.
     * @test
     */
    public function testGetSlackLinksNone(): void
    {
        $user = User::factory()->create();
        self::assertEmpty($user->slackLinks);
    }

    /**
     * Test getting SlackLinks for a user that has registered a channel.
     * @test
     */
    public function testGetSlackLinks(): void
    {
        $user = User::factory()->create();
        SlackLink::factory()->create(['user_id' => $user->id]);
        self::assertNotEmpty($user->slackLinks);
    }
}
