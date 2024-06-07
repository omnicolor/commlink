<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\VehicleModification;
use App\Models\Shadowrun5e\VehicleModificationArray;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

/**
 * Tests for the VehicleModificationArray class.
 * @group shadowrun
 * @group shadowrun5e
 */
#[Small]
final class VehicleModificationArrayTest extends TestCase
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
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->mods);
    }

    /**
     * Test adding an item to the array.
     */
    public function testAdd(): void
    {
        $this->mods[] = new VehicleModification('manual-control-override');
        self::assertNotEmpty($this->mods);
    }

    /**
     * Test that adding the wrong type to the array throws an exception.
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore-next-line
        $this->mods[] = new stdClass();
    }

    /**
     * Test that adding the wrong type to the array doesn't add it.
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->mods->offsetSet(mod: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->mods);
    }
}
