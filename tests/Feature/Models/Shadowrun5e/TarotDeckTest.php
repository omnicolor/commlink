<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\TarotDeck;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class TarotDeckTest extends TestCase
{
    /**
     * Test that a new TarotDeck has 74 cards.
     */
    public function testNewDeck(): void
    {
        $deck = new TarotDeck();
        self::assertCount(74, $deck);
        self::assertSame('The Awakened World', (string)$deck->drawOne());
    }
}
