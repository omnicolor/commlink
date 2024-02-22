<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\Weapon;
use App\Models\Shadowrun5e\WeaponArray;
use Tests\TestCase;
use TypeError;
use stdClass;

/**
 * Tests for the WeaponArray class.
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class WeaponArrayTest extends TestCase
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
        self::expectException(TypeError::class);
        // @phpstan-ignore-next-line
        $this->weapons[] = new stdClass();
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
            $this->weapons->offsetSet(weapon: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->weapons);
    }
}
