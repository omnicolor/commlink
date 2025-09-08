<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\Armor;
use Modules\Shadowrun5e\Models\ArmorArray;
use Override;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class ArmorArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var ArmorArray<Armor>
     */
    private ArmorArray $armors;

    /**
     * Set up a clean subject.
     */
    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->armors = new ArmorArray();
    }

    /**
     * Test an empty ArmorArray.
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->armors);
    }

    /**
     * Test adding a armor to the array.
     */
    public function testAdd(): void
    {
        $this->armors[] = new Armor('armor-jacket');
        self::assertNotEmpty($this->armors);
    }

    /**
     * Test that adding a non-armor to the array throws an exception.
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore offsetAssign.valueType
        $this->armors[] = new stdClass();
    }

    /**
     * Test that adding a non-armor to the array doesn't add it.
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore argument.type
            $this->armors->offsetSet(armor: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->armors);
    }
}
