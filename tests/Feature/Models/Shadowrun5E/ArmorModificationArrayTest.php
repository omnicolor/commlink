<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5E;

use App\Models\Shadowrun5E\ArmorModification;
use App\Models\Shadowrun5E\ArmorModificationArray;
use App\Models\Shadowrun5E\GearModification;

/**
 * Tests for the ArmorModificationArray class.
 * @covers \App\Models\Shadowrun5E\ArmorModificationArray
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class ArmorModificationArrayTest extends \Tests\TestCase
{
    /**
     * Subject under test.
     * @var ArmorModificationArray<ArmorModification|GearModification>
     */
    protected ArmorModificationArray $mods;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->mods = new ArmorModificationArray();
    }

    /**
     * Test an empty ArmorModificationArray.
     * @test
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->mods);
    }

    /**
     * Test adding an armor mod to the array.
     * @test
     */
    public function testAddArmorMod(): void
    {
        $this->mods[] = new ArmorModification('auto-injector');
        self::assertNotEmpty($this->mods);
    }

    /**
     * Test adding a gear mod to the array.
     * @test
     */
    public function testAddGearMod(): void
    {
        $this->mods[] = new GearModification('biomonitor');
        self::assertNotEmpty($this->mods);
    }

    /**
     * Test that adding a non-armor mod to the array throws an exception.
     * @test
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(\TypeError::class);
        self::expectExceptionMessage(
            'ArmorModificationArray only accepts Armor- or GearModification objects'
        );
        // @phpstan-ignore-next-line
        $this->mods[] = new \StdClass();
    }

    /**
     * Test that adding a non-armor mod to the array doesn't add it.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->mods->offsetSet(mod: new \StdClass());
        } catch (\TypeError $e) {
            // Ignored
        }
        self::assertEmpty($this->mods);
    }
}
