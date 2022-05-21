<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\Spirit;
use App\Models\Shadowrun5e\SpiritArray;

/**
 * Tests for the SpiritArray class.
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class SpiritArrayTest extends \Tests\TestCase
{
    /**
     * Subject under test.
     * @var SpiritArray<Spirit>
     */
    protected SpiritArray $spirits;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->spirits = new SpiritArray();
    }

    /**
     * Test an empty SpiritArray.
     * @test
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->spirits);
    }

    /**
     * Test adding a spirit to the array.
     * @test
     */
    public function testAdd(): void
    {
        $this->spirits[] = new Spirit('air');
        self::assertNotEmpty($this->spirits);
    }

    /**
     * Test that adding a non-spirit to the array throws an exception.
     * @test
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(\TypeError::class);
        // @phpstan-ignore-next-line
        $this->spirits[] = new \StdClass();
    }

    /**
     * Test that adding a non-spirit to the array doesn't add it.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->spirits->offsetSet(spirit: new \StdClass());
        } catch (\TypeError $e) {
            // Ignored
        }
        self::assertEmpty($this->spirits);
    }
}
