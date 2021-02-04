<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Shadowrun5E;

use App\Models\Shadowrun5E\GearModification;
use App\Models\Shadowrun5E\GearModificationArray;

/**
 * Tests for the GearModificationArray.
 * @covers \App\Models\Shadowrun5E\GearModificationArray
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 */
final class GearModificationArrayTest extends \Tests\TestCase
{
    /**
     * Subject under test.
     * @var GearModificationArray<GearModification>
     */
    protected GearModificationArray $mods;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->mods = new GearModificationArray();
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
    public function testAdd(): void
    {
        $this->mods[] = new GearModification('biomonitor');
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
     * Test that adding the wrong type to the array doesn't add the object.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->mods[] = new \StdClass();
        } catch (\TypeError $ex) {
            // Ignored
        }
        self::assertEmpty($this->mods);
    }
}
