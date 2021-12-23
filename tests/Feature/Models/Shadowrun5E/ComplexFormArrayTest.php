<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5E;

use App\Models\Shadowrun5E\ComplexForm;
use App\Models\Shadowrun5E\ComplexFormArray;

/**
 * Tests for the ComplexFormArray.
 * @covers \App\Models\Shadowrun5E\ComplexFormArray
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class ComplexFormArrayTest extends \Tests\TestCase
{
    /**
     * Subject under test.
     * @var ComplexFormArray<ComplexForm>
     */
    protected ComplexFormArray $forms;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->forms = new ComplexFormArray();
    }

    /**
     * Test an empty array.
     * @test
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->forms);
    }

    /**
     * Test adding to the array.
     * @test
     */
    public function testAdd(): void
    {
        $this->forms[] = new ComplexForm('cleaner');
        self::assertNotEmpty($this->forms);
    }

    /**
     * Test that adding the wrong type to the array throws an exception.
     * @test
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(\TypeError::class);
        // @phpstan-ignore-next-line
        $this->forms[] = new \StdClass();
    }

    /**
     * Test that adding the wrong type to the array doesn't add the object.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->forms->offsetSet(form: new \StdClass());
        } catch (\TypeError) {
            // Ignored
        }
        self::assertEmpty($this->forms);
    }
}
