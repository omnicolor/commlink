<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Campaign;
use App\Models\Character;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

use function is_subclass_of;

#[Group('character')]
#[Small]
final class CharacterTest extends TestCase
{
    use WithFaker;

    /**
     * Characters are required to have an owner.
     */
    public function testNoUser(): void
    {
        $character = new Character([
            'owner' => $this->faker->unique()->safeEmail,
        ]);
        self::expectException(ModelNotFoundException::class);
        $character->user();
    }

    /**
     * Load a character's owner.
     */
    public function testGetUser(): void
    {
        $user = User::factory()->create();
        $character = new Character(['owner' => $user->email->address]);
        self::assertSame(
            $user->email->address,
            $character->user()->email->address,
        );
    }

    /**
     * Test finding a character with no system returns a Character.
     */
    public function testBuildDefault(): void
    {
        $character = Character::factory()->create(['system' => 'unknown']);
        $character = Character::where('_id', $character->id)->firstOrFail();
        self::assertSame('unknown', $character->system);
        self::assertFalse(is_subclass_of($character, Character::class));
        $character->delete();
    }

    /**
     * Test finding a character that has a system returns a subclass of
     * Character.
     */
    public function testBuildSubclass(): void
    {
        $character = Character::factory()->create(['system' => 'shadowrun5e']);
        $character = Character::where('_id', $character->id)
            ->firstOrFail();
        self::assertSame('shadowrun5e', $character->system);
        self::assertInstanceOf(
            \Modules\Shadowrun5e\Models\Character::class,
            $character
        );

        // PHPStan reports that this is always true. testBuildDefault() asserts
        // that it's not.
        // @phpstan-ignore function.alreadyNarrowedType
        self::assertTrue(is_subclass_of($character, Character::class));

        $character->delete();
    }

    /**
     * Test getting the campaign attached to a character if they don't have one.
     */
    public function testCampaignNone(): void
    {
        $character = Character::factory()->make();
        self::assertNull($character->campaign());
    }

    /**
     * Test trying to get a campaign attached to a character if it is not found.
     */
    public function testCampaignNotFound(): void
    {
        $character = Character::factory()->make([
            'campaign_id' => 'not-found',
            'system' => 'shadowrun5e',
        ]);
        self::assertNull($character->campaign());
    }

    /**
     * Test getting the campaign attached to a character.
     */
    public function testCampaign(): void
    {
        $campaign = Campaign::factory()->create(['system' => 'shadowrun5e']);
        $character = Character::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'shadowrun5e',
        ]);
        self::assertNotNull($character->campaign());
    }

    /**
     * Test the getSystem() method from the GameSystem trait.
     */
    public function testGameSystem(): void
    {
        $character = Character::factory()->make(['system' => 'shadowrun5e']);
        self::assertSame('Shadowrun 5th Edition', $character->getSystem());
    }

    /**
     * Test the getSystem() method with an unknown system.
     */
    public function testGameSystemNotFound(): void
    {
        $character = Character::factory()->make(['system' => 'unknown']);
        self::assertSame('unknown', $character->getSystem());
    }
}
