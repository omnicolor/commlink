<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Expanse;

use App\Models\Expanse\Condition;
use App\Models\Expanse\ConditionArray;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

/**
 * Tests for the ConditionArray class.
 * @group expanse
 */
#[Small]
final class ConditionArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var ConditionArray<Condition>
     */
    protected ConditionArray $array;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->array = new ConditionArray();
    }

    public function testEmpty(): void
    {
        self::assertEmpty($this->array);
    }

    public function testAdd(): void
    {
        $this->array[] = new Condition('deafened');
        self::assertNotEmpty($this->array);
    }

    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore-next-line
        $this->array[] = new stdClass();
    }

    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->array->offsetSet(condition: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->array);
    }
}
