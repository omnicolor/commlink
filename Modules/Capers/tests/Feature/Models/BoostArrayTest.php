<?php

declare(strict_types=1);

namespace Modules\Capers\Tests\Feature\Models;

use Modules\Capers\Models\Boost;
use Modules\Capers\Models\BoostArray;
use Override;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

#[Group('capers')]
#[Small]
final class BoostArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var BoostArray<Boost>
     */
    protected BoostArray $boosts;

    /**
     * Set up a clean subject.
     */
    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->boosts = new BoostArray();
    }

    /**
     * Test an empty BoostArray.
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->boosts);
    }

    /**
     * Test adding a skill to the array.
     */
    public function testAdd(): void
    {
        $this->boosts[] = new Boost('foo', 'Foo', 'Foo description');
        self::assertNotEmpty($this->boosts);
    }

    /**
     * Test that adding a non-skill to the array throws an exception.
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore offsetAssign.valueType
        $this->boosts[] = new stdClass();
    }

    /**
     * Test that adding a non-skill to the array doesn't add it.
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore argument.type
            $this->boosts->offsetSet(boost: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->boosts);
    }
}
