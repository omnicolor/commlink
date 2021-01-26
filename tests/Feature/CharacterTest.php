<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Character;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

/**
 * Tests for the top-level Character class.
 * @group character
 */
final class CharacterTest extends \Tests\TestCase
{
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
}
