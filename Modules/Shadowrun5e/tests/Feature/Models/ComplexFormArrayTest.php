<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\ComplexForm;
use Modules\Shadowrun5e\Models\ComplexFormArray;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class ComplexFormArrayTest extends TestCase
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
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->forms);
    }

    /**
     * Test adding to the array.
     */
    public function testAdd(): void
    {
        $this->forms[] = new ComplexForm('cleaner');
        self::assertNotEmpty($this->forms);
    }

    /**
     * Test that adding the wrong type to the array throws an exception.
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore-next-line
        $this->forms[] = new stdClass();
    }

    /**
     * Test that adding the wrong type to the array doesn't add the object.
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->forms->offsetSet(form: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->forms);
    }
}