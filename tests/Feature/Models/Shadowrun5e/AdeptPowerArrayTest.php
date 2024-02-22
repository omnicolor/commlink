<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\AdeptPower;
use App\Models\Shadowrun5e\AdeptPowerArray;
use Tests\TestCase;
use TypeError;
use stdClass;

/**
 * Tests for the AdeptPowerArray class.
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class AdeptPowerArrayTest extends TestCase
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
        self::expectException(TypeError::class);
        // @phpstan-ignore-next-line
        $this->powers[] = new stdClass();
    }

    /**
     * Test that adding a non-power to the array doesn't add it.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->powers->offsetSet(power: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->powers);
    }
}
