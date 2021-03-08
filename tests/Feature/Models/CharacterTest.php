<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Character;
use App\Models\User;

/**
 * Tests for the top-level Character class.
 * @group character
 */
final class CharacterTest extends \Tests\TestCase
{
    /**
     * Character we're testing with.
     * @var Character
     */
    protected Character $character;

    /**
     * Faker instance.
     * @var \Faker\Generator
     */
    protected static \Faker\Generator $faker;

    /**
     * Set up the test suite.
     */
    public static function setUpBeforeClass(): void
    {
        self::$faker = \Faker\Factory::create();
    }

    /**
     * Clean up.
     */
    public function tearDown(): void
    {
        if (isset($this->character)) {
            $this->character->delete();
            unset($this->character);
        }
        parent::tearDown();
    }

    /**
     * Characters are required to have an owner.
     * @test
     */
    public function testNoUser(): void
    {
        $character = new Character([
            'owner' => self::$faker->unique()->safeEmail,
        ]);
        self::expectException(
            \Illuminate\Database\Eloquent\ModelNotFoundException::class
        );
        $character->user();
    }

    /**
     * Load a character's owner.
     * @test
     */
    public function testGetUser(): void
    {
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
        $this->character = Character::factory()
            ->create(['system' => 'unknown']);
        $character = Character::where('_id', $this->character->id)
            ->firstOrFail();
        self::assertSame('unknown', $character->system);

        // PHPStan reports that this is always true. We're asserting that it's
        // not.
        // @phpstan-ignore-next-line
        self::assertFalse(is_subclass_of($character, Character::class));
    }

    /**
     * Test finding a character that has a system returns a subclass of
     * \App\Model\Character.
     * @test
     */
    public function testBuildSubclass(): void
    {
        $this->character = Character::factory()
            ->create(['system' => 'shadowrun5e']);
        $character = Character::where('_id', $this->character->id)
            ->firstOrFail();
        self::assertSame('shadowrun5e', $character->system);
        self::assertInstanceOf(
            \App\Models\Shadowrun5E\Character::class,
            $character
        );

        // PHPStan reports that this is always true. testBuildDefault() asserts
        // that it's not.
        // @phpstan-ignore-next-line
        self::assertTrue(is_subclass_of($character, Character::class));
    }
}
