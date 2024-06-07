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
     * Characters are required to have an owner.
     * @medium
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
     */
    public function testBuildDefault(): void
    {
        $character = Character::factory()->create([
            'system' => 'unknown',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        /** @var Character */
        $character = Character::where('_id', $character->id)->firstOrFail();
        self::assertSame('unknown', $character->system);
        self::assertFalse(\is_subclass_of($character, Character::class));
        $character->delete();
    }

    /**
     * Test finding a character that has a system returns a subclass of
     * \App\Model\Character.
     */
    public function testBuildSubclass(): void
    {
        $character = Character::factory()->create([
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        /** @var Character */
        $character = Character::where('_id', $character->id)
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

        $character->delete();
    }

    /**
     * Test getting the campaign attached to a character if they don't have one.
     */
    public function testCampaignNone(): void
    {
        /** @var Character */
        $character = Character::factory()->make([
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        self::assertNull($character->campaign());
    }

    /**
     * Test trying to get a campaign attached to a character if it is not found.
     * @medium
     */
    public function testCampaignNotFound(): void
    {
        /** @var Character */
        $character = Character::factory()->make([
            'campaign_id' => 0,
            'system' => 'shadowrun5e',
        ]);
        self::assertNull($character->campaign());
    }

    /**
     * Test getting the campaign attached to a character.
     * @medium
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
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        self::assertNotNull($character->campaign());
    }

    /**
     * Test the getSystem() method from the GameSystem trait.
     */
    public function testGameSystem(): void
    {
        /** @var Character */
        $character = Character::factory()->make([
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        self::assertSame('Shadowrun 5th Edition', $character->getSystem());
    }

    /**
     * Test the getSystem() method with an unknown system.
     */
    public function testGameSystemNotFound(): void
    {
        /** @var Character */
        $character = Character::factory()->make([
            'system' => 'unknown',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        self::assertSame('unknown', $character->getSystem());
    }
}
