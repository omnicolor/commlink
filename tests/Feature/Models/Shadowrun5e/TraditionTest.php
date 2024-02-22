<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\Tradition;
use RuntimeException;
use Tests\TestCase;

/**
 * Unit tests for Tradition class.
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class TraditionTest extends TestCase
{
    /**
     * Test loading an invalid tradition.
     * @test
     */
    public function testInvalidTradition(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Tradition ID "invalid" not found');
        new Tradition('invalid');
    }

    /**
     * Test that the constructor sets the things it should set.
     * @test
     */
    public function testConstructor(): void
    {
        $tradition = new Tradition('norse');
        self::assertNotNull($tradition->description);
        self::assertEquals('Willpower + Logic', $tradition->drain);
        self::assertEquals(
            [
                'combat' => 'Guardian',
                'detection' => 'Earth',
                'health' => 'Plant',
                'illusion' => 'Air',
                'manipulation' => 'Fire',
            ],
            $tradition->elements
        );
        self::assertEquals('norse', $tradition->id);
        self::assertEquals('Norse', $tradition->name);
        self::assertEquals(4, $tradition->page);
        self::assertEquals('shadow-spells', $tradition->ruleset);
    }

    /**
     * Test the __toString method.
     * @test
     */
    public function testToString(): void
    {
        $tradition = new Tradition('norse');
        self::assertEquals('Norse', (string)$tradition);
    }

    /**
     * Test returning the drain attributes.
     * @test
     */
    public function testGetDrainAttributes(): void
    {
        $tradition = new Tradition('norse');
        self::assertEquals(
            ['Willpower', 'Logic'],
            $tradition->getDrainAttributes()
        );
    }
}
