<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Cyberpunkred;

use App\Models\Cyberpunkred\TarotCard;
use App\Models\Cyberpunkred\TarotDeck;
use Generator;
use InvalidArgumentException;

/**
 * @small
 */
final class TarotCardTest extends \Tests\TestCase
{
    /**
     * Provider for all of the different cards.
     * @return Generator<int, array<int, string>>
     */
    public function cardProvider(): Generator
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
     * @test
     */
    public function testAllCardsHaveDescriptions(string $cardName): void
    {
        $card = new TarotCard($cardName, '');
        self::assertNotEquals('', $card->getDescription());
    }

    /**
     * Test getting the description of an invalid card.
     * @test
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
     * @test
     */
    public function testAllCardsHaveEffects(string $cardName): void
    {
        $card = new TarotCard($cardName, '');
        self::assertNotEquals('', $card->getEffect());
    }

    /**
     * Test getting the effect of an invalid card.
     * @test
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
     * @test
     */
    public function testToString(string $cardName): void
    {
        $card = new TarotCard($cardName, 'unused');
        self::assertSame($cardName, (string)$card);
    }
}
