<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\Program;
use App\Models\Shadowrun5e\ProgramArray;
use Tests\TestCase;
use TypeError;
use stdClass;

/**
 * Tests for the ProgramArray.
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class ProgramArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var ProgramArray<Program>
     */
    protected ProgramArray $programs;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->programs = new ProgramArray();
    }

    /**
     * Test an empty array.
     * @test
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->programs);
    }

    /**
     * Test adding to the array.
     * @test
     */
    public function testAdd(): void
    {
        $this->programs[] = new Program('armor');
        self::assertNotEmpty($this->programs);
    }

    /**
     * Test that adding the wrong type to the array throws an exception.
     * @test
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore-next-line
        $this->programs[] = new stdClass();
    }

    /**
     * Test that adding the wrong type to the array doesn't add the object.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->programs->offsetSet(program: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->programs);
    }
}
