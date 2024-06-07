<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\Spell;
use App\Models\Shadowrun5e\SpellArray;
use Tests\TestCase;
use TypeError;
use stdClass;

/**
 * Tests for the SpellArray.
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class SpellArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var SpellArray<Spell>
     */
    protected SpellArray $spells;

    /**
     * Set up a clean subject.
     */
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
        // @phpstan-ignore-next-line
        $this->spells[] = new stdClass();
    }

    /**
     * Test that adding the wrong type to the array doesn't add the object.
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->spells->offsetSet(spell: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->spells);
    }
}
