<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\GearModification;
use App\Models\Shadowrun5e\GearModificationArray;
use Tests\TestCase;
use TypeError;
use stdClass;

/**
 * Tests for the GearModificationArray.
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class GearModificationArrayTest extends TestCase
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
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->mods);
    }

    /**
     * Test adding to the array.
     */
    public function testAdd(): void
    {
        $this->mods[] = new GearModification('biomonitor');
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
     * Test that adding the wrong type to the array doesn't add the object.
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
