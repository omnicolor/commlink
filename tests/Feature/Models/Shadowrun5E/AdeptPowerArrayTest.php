<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5E;

use App\Models\Shadowrun5E\AdeptPower;
use App\Models\Shadowrun5E\AdeptPowerArray;

/**
 * Tests for the AdeptPowerArray class.
 * @covers \App\Models\Shadowrun5E\AdeptPowerArray
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class AdeptPowerArrayTest extends \Tests\TestCase
{
    /**
     * Subject under test.
     * @var AdeptPowerArray<AdeptPower>
     */
    protected AdeptPowerArray $powers;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->powers = new AdeptPowerArray();
    }

    /**
     * Test an empty AdeptPowerArray.
     * @test
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->powers);
    }

    /**
     * Test adding a power to the array.
     * @test
     */
    public function testAdd(): void
    {
        $this->powers[] = new AdeptPower('improved-sense-direction-sense');
        self::assertNotEmpty($this->powers);
    }

    /**
     * Test that adding a non-power to the array throws an exception.
     * @test
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(\TypeError::class);
        // @phpstan-ignore-next-line
        $this->powers[] = new \StdClass();
    }

    /**
     * Test that adding a non-power to the array doesn't add it.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->powers[] = new \StdClass();
        } catch (\TypeError $e) {
            // Ignored
        }
        self::assertEmpty($this->powers);
    }
}
