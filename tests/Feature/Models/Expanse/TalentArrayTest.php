<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Expanse;

use App\Models\Expanse\Talent;
use App\Models\Expanse\TalentArray;

/**
 * Tests for the TalentArray class.
 * @covers \App\Models\Expanse\TalentArray
 * @group models
 * @group expanse
 */
final class TalentArrayTest extends \Tests\TestCase
{
    /**
     * Subject under test.
     * @var TalentArray<Talent>
     */
    protected TalentArray $array;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->array = new TalentArray();
    }

    /**
     * Test an empty array.
     * @test
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->array);
    }

    /**
     * Test adding a valid object to the array.
     * @test
     */
    public function testAdd(): void
    {
        $this->array[] = new Talent('fringer');
        self::assertNotEmpty($this->array);
    }

    /**
     * Test that adding an object of the wrong type throws an exception.
     * @test
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(\TypeError::class);
        // @phpstan-ignore-next-line
        $this->array[] = new \StdClass();
    }

    /**
     * Test that adding the wrong type to the array doesn't add it.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->array[] = new \StdClass();
        } catch (\TypeError $e) {
            // Ignored
        }
        self::assertEmpty($this->array);
    }
}
