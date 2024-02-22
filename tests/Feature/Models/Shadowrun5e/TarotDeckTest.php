<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\TarotDeck;
use Tests\TestCase;

/**
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class TarotDeckTest extends TestCase
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
