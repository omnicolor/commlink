<?php

declare(strict_types=1);

namespace Modules\StartrekAdventures\Tests\Feature\Models;

use Modules\Startrekadventures\Models\Talent;
use Modules\Startrekadventures\Models\TalentArray;
use Override;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

#[Group('startrekadventures')]
#[Small]
final class TalentArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var TalentArray<Talent>
     */
    protected TalentArray $talents;

    /**
     * Set up a clean subject.
     */
    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->talents = new TalentArray();
    }

    /**
     * Test an empty TalentArray.
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->talents);
    }

    /**
     * Test adding a talent to the array.
     */
    public function testAdd(): void
    {
        $this->talents[] = new Talent('bold-command');
        self::assertNotEmpty($this->talents);
    }

    /**
     * Test that adding a non-talent to the array throws an exception.
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore offsetAssign.valueType
        $this->talents[] = new stdClass();
    }

    /**
     * Test that adding a non-talent to the array doesn't add it.
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore argument.type
            $this->talents->offsetSet(index: 0, talent: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->talents);
    }
}
