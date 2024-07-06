<?php

declare(strict_types=1);

namespace Modules\Subversion\Tests\Feature\Models;

use Modules\Subversion\Models\Gear;
use Modules\Subversion\Models\GearArray;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

#[Group('subversion')]
#[Small]
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
