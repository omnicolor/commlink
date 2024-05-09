<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Subversion;

use App\Models\Subversion\Gear;
use App\Models\Subversion\GearArray;
use Tests\TestCase;
use TypeError;
use stdClass;

/**
 * Tests for the GearArray class.
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class GearArrayTest extends TestCase
{
    /**
     * @var GearArray<Gear>
     */
    protected GearArray $gears;

    public function setUp(): void
    {
        parent::setUp();
        $this->gears = new GearArray();
    }

    public function testEmpty(): void
    {
        self::assertEmpty($this->gears);
    }

    public function testAdd(): void
    {
        $this->gears[] = new Gear('paylo');
        self::assertNotEmpty($this->gears);
    }

    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore-next-line
        $this->gears[] = new stdClass();
    }

    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->gears->offsetSet(gear: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->gears);
    }
}
