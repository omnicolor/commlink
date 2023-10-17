<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\ForceTrait;
use Tests\TestCase;

/**
 * Tests for the Force trait.
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class ForceTraitTest extends TestCase
{
    /**
     * Subject under test.
     * @var mixed
     */
    protected $force;

    /**
     * Set up the subject under test.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->force = $this->getMockForTrait(ForceTrait::class);
    }

    /**
     * Test calculating when adding a number.
     * @test
     */
    public function testAdd(): void
    {
        self::assertSame(3, $this->force->convertFormula('F+2', 'F', 1));
        self::assertSame(4, $this->force->convertFormula('L+1', 'L', 3));
        self::assertSame(5, $this->force->convertFormula('L+1', 'L', 4));
        self::assertSame(8, $this->force->convertFormula('F+3', 'F', 5));
    }

    /**
     * Test calculating when subtracting a number.
     * @test
     */
    public function testSubtract(): void
    {
        self::assertSame(1, $this->force->convertFormula('F-1', 'F', 2));
        self::assertSame(2, $this->force->convertFormula('L-2', 'L', 4));
        self::assertSame(3, $this->force->convertFormula('L-3', 'L', 6));
        self::assertSame(4, $this->force->convertFormula('F-4', 'F', 8));
    }

    /**
     * Test calculating when dividing.
     * @test
     */
    public function testDivide(): void
    {
        self::assertSame(1, $this->force->convertFormula('F/2', 'F', 2));
        self::assertSame(2, $this->force->convertFormula('L/2', 'L', 4));
    }

    /**
     * Test calculating when multiplying.
     * @test
     */
    public function testMultiply(): void
    {
        self::assertSame(2, $this->force->convertFormula('S*2', 'S', 1));
        self::assertSame(6, $this->force->convertFormula('M*3', 'M', 2));
    }

    /**
     * Test calulating an invalid string.
     * @test
     */
    public function testInvalid(): void
    {
        self::assertSame(0, $this->force->convertFormula('Test', '1', 1));
        self::assertSame(12, $this->force->convertFormula('TT+1', 'T', 1));
        self::assertSame(3, $this->force->convertFormula('T^2', 'T', 3));
        self::assertSame(1, $this->force->convertFormula('+T+1-2', 'T', 2));
        self::assertSame(0, $this->force->convertFormula('', 'L', 1));
    }
}
