<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5E;

use App\Models\Shadowrun5E\WeaponModification;
use App\Models\Shadowrun5E\WeaponModificationArray;

/**
 * Tests for the WeaponModificationArray.
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class WeaponModificationArrayTest extends \Tests\TestCase
{
    /**
     * Subject under test.
     * @var WeaponModificationArray
     */
    protected WeaponModificationArray $mods;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->mods = new WeaponModificationArray();
    }

    /**
     * Test an empty array.
     * @test
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->mods);
    }

    /**
     * Test adding to the array.
     * @test
     */
    public function testAppend(): void
    {
        $this->mods[] = new WeaponModification('bayonet');
        self::assertNotEmpty($this->mods);
    }

    /**
     * Test setting up a weapon's slots.
     * @test
     */
    public function testInitializeSlots(): void
    {
        $this->mods['barrel'] = null;
        $this->mods['internal'] = null;

        self::assertCount(2, $this->mods);
    }

    /**
     * Test adding a modification to a weapon's slot.
     * @test
     */
    public function testAddToSlot(): void
    {
        $this->mods['barrel'] = new WeaponModification('bayonet');
        self::assertSame('Bayonet', (string)$this->mods['barrel']);
    }

    /**
     * Test that adding the wrong type to the array throws an exception.
     * @test
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(\TypeError::class);
        // @phpstan-ignore-next-line
        $this->mods[] = new \StdClass();
    }

    /**
     * Test that adding the wrong type to the array doesn't add the object.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->mods->offsetSet(mod: new \stdClass());
        } catch (\TypeError) {
            // Ignored
        }
        self::assertEmpty($this->mods);
    }
}
