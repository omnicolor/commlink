<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\Spirit;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class ForceTraitTest extends TestCase
{
    protected Spirit $force;

    public function setUp(): void
    {
        parent::setUp();
        $this->force = new Spirit('air');
    }

    /**
     * Test calculating when adding a number.
     */
    public function testAdd(): void
    {
        self::assertSame(3, $this->force::convertFormula('F+2', 'F', 1));
        self::assertSame(4, $this->force::convertFormula('L+1', 'L', 3));
        self::assertSame(5, $this->force::convertFormula('L+1', 'L', 4));
        self::assertSame(8, $this->force::convertFormula('F+3', 'F', 5));
    }

    /**
     * Test calculating when subtracting a number.
     */
    public function testSubtract(): void
    {
        self::assertSame(1, $this->force::convertFormula('F-1', 'F', 2));
        self::assertSame(2, $this->force::convertFormula('L-2', 'L', 4));
        self::assertSame(3, $this->force::convertFormula('L-3', 'L', 6));
        self::assertSame(4, $this->force::convertFormula('F-4', 'F', 8));
    }

    /**
     * Test calculating when dividing.
     */
    public function testDivide(): void
    {
        self::assertSame(1, $this->force::convertFormula('F/2', 'F', 2));
        self::assertSame(2, $this->force::convertFormula('L/2', 'L', 4));
    }

    /**
     * Test calculating when multiplying.
     */
    public function testMultiply(): void
    {
        self::assertSame(2, $this->force::convertFormula('S*2', 'S', 1));
        self::assertSame(6, $this->force::convertFormula('M*3', 'M', 2));
    }

    /**
     * Test calculating an invalid string.
     */
    public function testInvalid(): void
    {
        self::assertSame(0, $this->force::convertFormula('Test', '1', 1));
        self::assertSame(12, $this->force::convertFormula('TT+1', 'T', 1));
        self::assertSame(3, $this->force::convertFormula('T^2', 'T', 3));
        self::assertSame(1, $this->force::convertFormula('+T+1-2', 'T', 2));
        self::assertSame(0, $this->force::convertFormula('', 'L', 1));
    }

    /**
     * Test that it handles left to right order of operations correctly.
     */
    public function testSimpleButLong(): void
    {
        self::assertSame(
            -19,
            $this->force::convertFormula('1+2-8+24-48+10', 'x', 1),
        );
        self::assertSame(
            2,
            $this->force::convertFormula('2*2/4*8/4', 'x', 1),
        );
        self::assertSame(
            5,
            $this->force::convertFormula('2+2*2-2/2', 'F', 0),
        );
    }
}
