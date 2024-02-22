<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\Armor;
use App\Models\Shadowrun5e\ArmorArray;
use Tests\TestCase;
use TypeError;
use stdClass;

/**
 * Tests for the ArmorArray class.
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class ArmorArrayTest extends TestCase
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
        self::expectException(TypeError::class);
        // @phpstan-ignore-next-line
        $this->armors[] = new stdClass();
    }

    /**
     * Test that adding a non-armor to the array doesn't add it.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->armors->offsetSet(armor: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->armors);
    }
}
