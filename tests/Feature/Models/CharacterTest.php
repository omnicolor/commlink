<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Campaign;
use App\Models\Character;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Tests for the top-level Character class.
 * @group character
 * @small
 */
final class CharacterTest extends TestCase
{
    use WithFaker;

    /**
     * Character we're testing with.
     * @var ?Character
     */
    protected ?Character $character;

    /**
     * Clean up.
     */
    public function tearDown(): void
    {
        if (isset($this->character)) {
            $this->character->delete();
            $this->character = null;
        }
        parent::tearDown();
    }

    /**
     * Characters are required to have an owner.
     * @medium
     * @test
     */
    public function testNoUser(): void
    {
        $character = new Character([
            'owner' => $this->faker->unique()->safeEmail,
        ]);
        self::expectException(
            \Illuminate\Database\Eloquent\ModelNotFoundException::class
        );
        $character->user();
    }

    /**
     * Load a character's owner.
     * @medium
     * @test
     */
    public function testGetUser(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $character = new Character(['owner' => $user->email]);
        self::assertInstanceOf(User::class, $character->user());
    }

    /**
     * Test finding a character with no system returns an \App\Model\Character.
     * @test
     */
    public function testBuildDefault(): void
    {
        // @phpstan-ignore-next-line
        $this->character = Character::factory()
            ->create(['system' => 'unknown']);
        // @phpstan-ignore-next-line
        $character = Character::where('_id', $this->character->id)
            ->firstOrFail();
        self::assertSame('unknown', $character->system);

        // PHPStan reports that this is always true. We're asserting that it's
        // not.
        // @phpstan-ignore-next-line
        self::assertFalse(\is_subclass_of($character, Character::class));
    }

    /**
     * Test finding a character that has a system returns a subclass of
     * \App\Model\Character.
     * @test
     */
    public function testBuildSubclass(): void
    {
        // @phpstan-ignore-next-line
        $this->character = Character::factory()
            ->create(['system' => 'shadowrun5e']);
        // @phpstan-ignore-next-line
        $character = Character::where('_id', $this->character->id)
            ->firstOrFail();
        self::assertSame('shadowrun5e', $character->system);
        self::assertInstanceOf(
            \App\Models\Shadowrun5e\Character::class,
            $character
        );

        // PHPStan reports that this is always true. testBuildDefault() asserts
        // that it's not.
        // @phpstan-ignore-next-line
        self::assertTrue(\is_subclass_of($character, Character::class));
    }

    /**
     * Test getting the campaign attached to a character if they don't have one.
     * @test
     */
    public function testCampaignNone(): void
    {
        /** @var Character */
        $character = Character::factory()->make();
        self::assertNull($character->campaign());
    }

    /**
     * Test getting the campaign attached to a character.
     * @medium
     * @test
     */
    public function testCampaign(): void
    {
        $campaign = Campaign::factory()->create([
            'system' => 'shadowrun5e',
        ]);
        /** @var Character */
        $character = Character::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'shadowrun5e',
        ]);
        self::assertNotNull($character->campaign());
    }

    /**
     * Test the getSystem() method from the GameSystem trait.
     * @test
     */
    public function testGameSystem(): void
    {
        /** @var Character */
        $character = Character::factory()->make(['system' => 'shadowrun5e']);
        self::assertSame('Shadowrun 5th Edition', $character->getSystem());
    }

    /**
     * Test the getSystem() method with an unknown system.
     * @test
     */
    public function testGameSystemNotFound(): void
    {
        /** @var Character */
        $character = Character::factory()->make(['system' => 'unknown']);
        self::assertSame('unknown', $character->getSystem());
    }
}
