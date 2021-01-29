<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Shadowrun5E;

use App\Models\Shadowrun5E\AdeptPower;
use App\Models\Shadowrun5E\Armor;
use App\Models\Shadowrun5E\ArmorArray;

/**
 * Tests for the ArmorArray class.
 * @covers \App\Models\Shadowrun5E\ArmorArray
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 */
final class ArmorArrayTest extends \Tests\TestCase
{
    /**
     * Subject under test.
     * @var ArmorArray<Armor>
     */
    protected ArmorArray $armors;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->armors = new ArmorArray();
    }

    /**
     * Test an empty ArmorArray.
     * @test
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->armors);
    }

    /**
     * Test adding a armor to the array.
     * @test
     */
    public function testAdd(): void
    {
        $this->armors[] = new Armor('armor-jacket');
        self::assertNotEmpty($this->armors);
    }

    /**
     * Test that adding a non-armor to the array throws an exception.
     * @test
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(\TypeError::class);
        // @phpstan-ignore-next-line
        $this->armors[] = new AdeptPower('improved-sense-direction-sense');
    }

    /**
     * Test that adding a non-armor to the array doesn't add it.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->armors[] = new AdeptPower('improved-sense-direction-sense');
        } catch (\TypeError $e) {
            // Ignored
        }
        self::assertEmpty($this->armors);
    }
}
