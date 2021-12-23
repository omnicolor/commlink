<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5E;

use App\Models\Shadowrun5E\Spell;
use App\Models\Shadowrun5E\SpellArray;

/**
 * Tests for the SpellArray.
 * @covers \App\Models\Shadowrun5E\SpellArray
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class SpellArrayTest extends \Tests\TestCase
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
     * @test
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->spells);
    }

    /**
     * Test adding to the array.
     * @test
     */
    public function testAdd(): void
    {
        $this->spells[] = new Spell('control-emotions');
        self::assertNotEmpty($this->spells);
    }

    /**
     * Test that adding the wrong type to the array throws an exception.
     * @test
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(\TypeError::class);
        // @phpstan-ignore-next-line
        $this->spells[] = new \StdClass();
    }

    /**
     * Test that adding the wrong type to the array doesn't add the object.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->spells->offsetSet(spell: new \StdClass());
        } catch (\TypeError $ex) {
            // Ignored
        }
        self::assertEmpty($this->spells);
    }
}
