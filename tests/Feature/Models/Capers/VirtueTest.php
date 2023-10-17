<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Capers;

use App\Models\Capers\Virtue;
use App\Models\Card;
use RuntimeException;
use Tests\TestCase;

/**
 * Tests for Capers virtues.
 * @group capers
 * @small
 */
final class VirtueTest extends TestCase
{
    /**
     * Test trying to create a virtue that doesn't exist.
     * @test
     */
    public function testInvalidVirtue(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Virtue ID "invalid" is invalid');
        new Virtue('invalid');
    }

    /**
     * Test loading a virtue.
     * @test
     */
    public function testVirtue(): void
    {
        $virtue = new Virtue('loyal');
        self::assertSame('Loyal', (string)$virtue);
        self::assertSame(
            'Once you have given your word, you never break it.',
            $virtue->description
        );
        self::assertSame('loyal', $virtue->id);
    }

    /**
     * Test getting all virtues.
     * @test
     */
    public function testAll(): void
    {
        $virtues = Virtue::all();
        self::assertNotEmpty($virtues);
        self::assertSame('Loyal', (string)$virtues['loyal']);
    }

    /**
     * Test trying to find a virtue for a card with an invalid value.
     * @test
     */
    public function testFindForInvalidCard(): void
    {
        $card = new Card('Knave', 'Batons');
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Virtue not found for KnaveBatons');
        Virtue::findForCard($card);
    }

    /**
     * Test trying to find a virtue for a card.
     * @test
     */
    public function testFindForCard(): void
    {
        $virtue = Virtue::findForCard(new Card('8', '♣'));
        self::assertSame('honest', $virtue->id);
        self::assertSame('You do not lie... ever.', $virtue->description);
    }
}
