<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\Tradition;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class TraditionTest extends TestCase
{
    /**
     * Test loading an invalid tradition.
     */
    public function testInvalidTradition(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Tradition ID "invalid" not found');
        new Tradition('invalid');
    }

    /**
     * Test that the constructor sets the things it should set.
     */
    public function testConstructor(): void
    {
        $tradition = new Tradition('norse');
        self::assertSame('Willpower + Logic', $tradition->drain);
        self::assertSame(
            [
                'combat' => 'Guardian',
                'detection' => 'Earth',
                'health' => 'Plant',
                'illusion' => 'Air',
                'manipulation' => 'Fire',
            ],
            $tradition->elements
        );
        self::assertSame('norse', $tradition->id);
        self::assertSame('Norse', $tradition->name);
        self::assertSame(4, $tradition->page);
        self::assertSame('shadow-spells', $tradition->ruleset);
    }

    /**
     * Test the __toString method.
     */
    public function testToString(): void
    {
        $tradition = new Tradition('norse');
        self::assertSame('Norse', (string)$tradition);
    }

    /**
     * Test returning the drain attributes.
     */
    public function testGetDrainAttributes(): void
    {
        $tradition = new Tradition('norse');
        self::assertSame(
            ['Willpower', 'Logic'],
            $tradition->getDrainAttributes()
        );
    }
}
