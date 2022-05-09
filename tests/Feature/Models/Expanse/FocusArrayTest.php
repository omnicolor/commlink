<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Expanse;

use App\Models\Expanse\Focus;
use App\Models\Expanse\FocusArray;

/**
 * Tests for the FocusArray class.
 * @group models
 * @group expanse
 * @small
 */
final class FocusArrayTest extends \Tests\TestCase
{
    /**
     * Subject under test.
     * @var FocusArray<Focus>
     */
    protected FocusArray $array;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->array = new FocusArray();
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
        $this->array[] = new Focus('crafting');
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
            $this->array->offsetSet(focus: new \StdClass());
        } catch (\TypeError $e) {
            // Ignored
        }
        self::assertEmpty($this->array);
    }
}
