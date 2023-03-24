<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Capers;

use App\Models\Capers\Identity;
use App\Models\Card;
use RuntimeException;

/**
 * Tests for Capers identities.
 * @group capers
 * @small
 */
final class IdentityTest extends \Tests\TestCase
{
    /**
     * Try to create an invalid Identity.
     * @test
     */
    public function testInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Identity ID "invalid" is invalid');
        new Identity('invalid');
    }

    /**
     * Test creating a valid Identity.
     * @test
     */
    public function testIdentity(): void
    {
        $identity = new Identity('rebel');
        self::assertSame('Rebel', (string)$identity);
        self::assertSame('rebel', $identity->id);
        self::assertSame(
            'You seek to upset the status quo in almost everything you do. '
                . 'Gain Moxie when you change the status quo or change '
                . 'someone’s opinion on something controversial.',
            $identity->description
        );
    }

    /**
     * Test getting all identities.
     * @test
     */
    public function testAll(): void
    {
        $identities = Identity::all();
        self::assertNotEmpty($identities);
        $martyr = $identities['martyr'];
        self::assertSame('Martyr', (string)$martyr);
    }

    /**
     * Test finding an identity for a joker.
     * @test
     */
    public function testFindForJoker(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Joker drawn, draw again');
        Identity::findForCard(new Card('Good Joker', ''));
    }

    /**
     * @return array<int, array<int, Card|string>>
     */
    public static function findForCardProvider(): array
    {
        return [
            [new Card('5', '♦'), 'Crackerjack'],
            [new Card('5', '♣'), 'Reveler'],
            [new Card('2', '♥'), 'Autocrat'],
            [new Card('2', '♠'), 'Planner'],
        ];
    }

    /**
     * Test finding an identity for a normal card.
     * @dataProvider findForCardProvider
     * @param Card $card
     * @param string $expectedIdentity
     * @test
     */
    public function testFindForCard(Card $card, string $expectedIdentity): void
    {
        $identity = Identity::findForCard($card);
        self::assertSame($expectedIdentity, $identity->name);
    }

    /**
     * Test trying to find an identity for a card with an invalid suit.
     * @test
     */
    public function testFindForCardInvalidSuit(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Invalid suit');
        Identity::findForCard(new Card('4', 'Swords'));
    }

    /**
     * Test trying to find an identity of a valid suit but invalid value.
     * @test
     */
    public function testFindForCardInvalidValue(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Identity not found for 1♠');
        Identity::findForCard(new Card('1', '♠'));
    }
}
