<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Tests\Feature\Models;

use Generator;
use InvalidArgumentException;
use Modules\Cyberpunkred\Models\TarotCard;
use Modules\Cyberpunkred\Models\TarotDeck;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Small]
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
     */
    #[DataProvider('cardProvider')]
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
     */
    #[DataProvider('cardProvider')]
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
     */
    #[DataProvider('cardProvider')]
    public function testToString(string $cardName): void
    {
        $card = new TarotCard($cardName, 'unused');
        self::assertSame($cardName, (string)$card);
    }
}
