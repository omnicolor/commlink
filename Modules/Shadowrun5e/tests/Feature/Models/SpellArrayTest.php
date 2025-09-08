<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\Spell;
use Modules\Shadowrun5e\Models\SpellArray;
use Override;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class SpellArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var SpellArray<Spell>
     */
    private SpellArray $spells;

    /**
     * Set up a clean subject.
     */
    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->spells = new SpellArray();
    }

    /**
     * Test an empty array.
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->spells);
    }

    /**
     * Test adding to the array.
     */
    public function testAdd(): void
    {
        $this->spells[] = new Spell('control-emotions');
        self::assertNotEmpty($this->spells);
    }

    /**
     * Test that adding the wrong type to the array throws an exception.
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore offsetAssign.valueType
        $this->spells[] = new stdClass();
    }

    /**
     * Test that adding the wrong type to the array doesn't add the object.
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore argument.type
            $this->spells->offsetSet(spell: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->spells);
    }
}
