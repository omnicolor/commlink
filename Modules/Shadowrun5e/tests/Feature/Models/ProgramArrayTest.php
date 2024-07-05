<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\Program;
use Modules\Shadowrun5e\Models\ProgramArray;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
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
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->programs);
    }

    /**
     * Test adding to the array.
     */
    public function testAdd(): void
    {
        $this->programs[] = new Program('armor');
        self::assertNotEmpty($this->programs);
    }

    /**
     * Test that adding the wrong type to the array throws an exception.
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore-next-line
        $this->programs[] = new stdClass();
    }

    /**
     * Test that adding the wrong type to the array doesn't add the object.
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
