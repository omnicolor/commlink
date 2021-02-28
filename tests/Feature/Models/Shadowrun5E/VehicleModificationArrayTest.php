<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5E;

use App\Models\Shadowrun5E\VehicleModification;
use App\Models\Shadowrun5E\VehicleModificationArray;

/**
 * Tests for the VehicleModificationArray class.
 * @covers \App\Models\Shadowrun5E\VehicleModificationArray
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 */
final class VehicleModificationArrayTest extends \Tests\TestCase
{
    /**
     * Subject under test.
     * @var VehicleModificationArray<VehicleModification>
     */
    protected VehicleModificationArray $mods;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->mods = new VehicleModificationArray();
    }

    /**
     * Test an empty VehicleModificationArray.
     * @test
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->mods);
    }

    /**
     * Test adding an item to the array.
     * @test
     */
    public function testAdd(): void
    {
        $this->mods[] = new VehicleModification('manual-control-override');
        self::assertNotEmpty($this->mods);
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
     * Test that adding the wrong type to the array doesn't add it.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->mods[] = new \StdClass();
        } catch (\TypeError $e) {
            // Ignored
        }
        self::assertEmpty($this->mods);
    }
}
