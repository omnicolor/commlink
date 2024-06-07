<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Cyberpunkred;

use App\Models\Cyberpunkred\TarotCard;
use App\Models\Cyberpunkred\TarotDeck;
use Generator;
use InvalidArgumentException;
use Tests\TestCase;

/**
 * @small
 */
final class TarotCardTest extends TestCase
{
    /**
     * Provider for all of the different cards.
     * @return Generator<int, array<int, string>>
     */
    public static function cardProvider(): Generator
    {
        $deck = new TarotDeck();
        foreach ($deck->majorArcana as $cardName) {
            yield [$cardName];
        }
    }

    /**
     * Test that all of the cards have descriptions.
     * @dataProvider cardProvider
     * @param string $cardName
     */
    public function testAllCardsHaveDescriptions(string $cardName): void
    {
        $card = new TarotCard($cardName, '');
        self::assertNotEquals('', $card->getDescription());
    }

    /**
     * Test getting the description of an invalid card.
     */
    public function testDescriptionOfInvalidCard(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Invalid card');
        (new TarotCard('invalid', ''))->getDescription();
    }

    /**
     * Test that all of the cards have effects.
     * @dataProvider cardProvider
     * @param string $cardName
     */
    public function testAllCardsHaveEffects(string $cardName): void
    {
        $card = new TarotCard($cardName, '');
        self::assertNotEquals('', $card->getEffect());
    }

    /**
     * Test getting the effect of an invalid card.
     */
    public function testEffectOfInvalidCard(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Invalid card');
        (new TarotCard('invalid', ''))->getEffect();
    }

    /**
     * Test converting Night City Tarot cards to strings.
     * @dataProvider cardProvider
     * @param string $cardName
     */
    public function testToString(string $cardName): void
    {
        $card = new TarotCard($cardName, 'unused');
        self::assertSame($cardName, (string)$card);
    }
}
