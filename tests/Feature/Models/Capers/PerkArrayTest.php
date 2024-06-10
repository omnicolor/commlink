<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Capers;

use App\Models\Capers\Perk;
use App\Models\Capers\PerkArray;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

#[Group('capers')]
#[Small]
final class PerkArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var PerkArray<Perk>
     */
    protected PerkArray $perks;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->perks = new PerkArray();
    }

    /**
     * Test an empty PerkArray.
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->perks);
    }

    /**
     * Test adding a perk to the array.
     */
    public function testAdd(): void
    {
        $this->perks[] = new Perk('lucky', []);
        self::assertNotEmpty($this->perks);
    }

    /**
     * Test that adding a non-perk to the array throws an exception.
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore-next-line
        $this->perks[] = new stdClass();
    }

    /**
     * Test that adding a non-perk to the array doesn't add it.
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->perks->offsetSet(perk: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->perks);
    }
}
