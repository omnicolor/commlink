<?php

declare(strict_types=1);

namespace Modules\Expanse\Tests\Feature\Models;

use Modules\Expanse\Models\Talent;
use Modules\Expanse\Models\TalentArray;
use Override;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

#[Group('expanse')]
#[Small]
final class TalentArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var TalentArray<Talent>
     */
    protected TalentArray $array;

    /**
     * Set up a clean subject.
     */
    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->array = new TalentArray();
    }

    public function testEmpty(): void
    {
        self::assertEmpty($this->array);
    }

    public function testAdd(): void
    {
        $this->array[] = new Talent('fringer');
        self::assertNotEmpty($this->array);
    }

    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore offsetAssign.valueType
        $this->array[] = new stdClass();
    }

    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore argument.type
            $this->array->offsetSet(index: 1, talent: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->array);
    }
}
