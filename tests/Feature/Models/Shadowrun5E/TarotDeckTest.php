<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5E;

use App\Models\Shadowrun5E\TarotDeck;

/**
 * @small
 */
final class TarotDeckTest extends \Tests\TestCase
{
    /**
     * Test that a new TarotDeck has 74 cards.
     * @test
     */
    public function testNewDeck(): void
    {
        $deck = new TarotDeck();
        self::assertCount(74, $deck);
        self::assertSame('The Awakened World', (string)$deck->drawOne());
    }
}
