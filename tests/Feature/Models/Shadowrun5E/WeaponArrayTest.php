<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5E;

use App\Models\Shadowrun5E\Weapon;
use App\Models\Shadowrun5E\WeaponArray;

/**
 * Tests for the WeaponArray class.
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class WeaponArrayTest extends \Tests\TestCase
{
    /**
     * Subject under test.
     * @var WeaponArray<Weapon>
     */
    protected WeaponArray $weapons;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->weapons = new WeaponArray();
    }

    /**
     * Test an empty WeaponArray.
     * @test
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->weapons);
    }

    /**
     * Test adding a weapon to the array.
     * @test
     */
    public function testAdd(): void
    {
        $this->weapons[] = new Weapon('ak-98');
        self::assertNotEmpty($this->weapons);
    }

    /**
     * Test that adding a non-weapon to the array throws an exception.
     * @test
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(\TypeError::class);
        // @phpstan-ignore-next-line
        $this->weapons[] = new \StdClass();
        self::assertEmpty($this->weapons);
    }

    /**
     * Test that adding a non-weapon to the array doesn't add it.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->weapons->offsetSet(weapon: new \StdClass());
        } catch (\TypeError $e) {
            // Ignored
        }
        self::assertEmpty($this->weapons);
    }
}
