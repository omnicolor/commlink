<?php

declare(strict_types=1);

namespace Modules\Capers\Tests\Feature\Models;

use Modules\Capers\Models\Power;
use Modules\Capers\Models\PowerArray;
use Override;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

#[Group('capers')]
#[Small]
final class PowerArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var PowerArray<Power>
     */
    protected PowerArray $powers;

    /**
     * Set up a clean subject.
     */
    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->powers = new PowerArray();
    }

    /**
     * Test an empty PowerArray.
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->powers);
    }

    /**
     * Test adding a skill to the array.
     */
    public function testAdd(): void
    {
        $this->powers[] = new Power('acid-stream');
        self::assertNotEmpty($this->powers);
    }

    /**
     * Test that adding a non-skill to the array throws an exception.
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore offsetAssign.valueType
        $this->powers[] = new stdClass();
    }

    /**
     * Test that adding a non-skill to the array doesn't add it.
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore argument.type
            $this->powers->offsetSet(power: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->powers);
    }
}
