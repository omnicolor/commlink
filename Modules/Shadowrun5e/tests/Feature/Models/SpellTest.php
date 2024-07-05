<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\Spell;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class SpellTest extends TestCase
{
    /**
     * Test trying to load an invalid spell.
     */
    public function testLoadInvalid(): void
    {
        Spell::$spells = null;
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Spell ID "foo" is invalid');
        new Spell('foo');
    }

    /**
     * Test the constructor.
     */
    public function testConstructor(): void
    {
        $spell = new Spell('control-emotions');
        self::assertEquals('Manipulation', $spell->category);
        self::assertEquals('', $spell->damage);
        self::assertEquals('F-1', $spell->drain);
        self::assertEquals('S', $spell->duration);
        self::assertEquals('control-emotions', $spell->id);
        self::assertEquals('Control Emotions', $spell->name);
        self::assertEquals(21, $spell->page);
        self::assertEquals('LOS', $spell->range);
        self::assertEquals('shadow-spells', $spell->ruleset);
        self::assertEquals(['mental'], $spell->tags);
        self::assertEquals('M', $spell->type);
    }

    /**
     * Test the __toString() method.
     */
    public function testToString(): void
    {
        $spell = new Spell('control-emotions');
        self::assertEquals('Control Emotions', (string)$spell);
    }

    /**
     * Test trying to get the drain before the force has been set.
     */
    public function testGetDrainForceNotSet(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Force has not been set');
        $spell = new Spell('control-emotions');
        $spell->getDrain();
    }

    /**
     * Test getting the drain for a spell.
     */
    public function testGetDrain(): void
    {
        $spell = new Spell('control-emotions');
        $spell->setForce(6);
        self::assertEquals(5, $spell->getDrain());

        $spell->setForce(3);
        self::assertEquals(2, $spell->getDrain());
    }

    /**
     * Test failing to find a spell by name.
     */
    public function testFindByNameNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Spell "Not Found" was not found');
        Spell::findByName('Not Found');
    }

    /**
     * Test finding a spell by name.
     */
    public function testFindByName(): void
    {
        $spell = Spell::findByName('Control Emotions');
        self::assertSame('shadow-spells', $spell->ruleset);
    }
}
