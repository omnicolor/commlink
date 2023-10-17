<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Capers;

use App\Models\Capers\Vice;
use App\Models\Card;
use RuntimeException;
use Tests\TestCase;

/**
 * Tests for Capers vice.
 * @group capers
 * @small
 */
final class ViceTest extends TestCase
{
    /**
     * Test loading an invalid vice.
     * @test
     */
    public function testInvalidVice(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Vice ID "invalid" is invalid');
        new Vice('invalid');
    }

    /**
     * Test loading a vice.
     * @test
     */
    public function testVice(): void
    {
        $vice = new Vice('temper');
        self::assertSame('Temper', (string)$vice);
        self::assertSame('temper', $vice->id);
        self::assertSame('You anger easily.', $vice->description);
        self::assertSame('Q', $vice->card);
    }

    /**
     * Test getting all vices.
     * @test
     */
    public function testAll(): void
    {
        $vices = Vice::all();
        self::assertNotEmpty($vices);
        self::assertSame('Vain', (string)$vices['vain']);
    }

    /**
     * Test trying to find a vice for an invalid card.
     * @test
     */
    public function testFindForInvalidCard(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Vice not found for 30☃');
        Vice::findForCard(new Card('30', '☃'));
    }

    /**
     * Test trying to find a vice for a card.
     * @test
     */
    public function testFindForCard(): void
    {
        $vice = Vice::findForCard(new Card('A', '☃'));
        self::assertSame('Vengeful', (string)$vice);
        self::assertSame(
            'You constantly have a score to settle.',
            $vice->description
        );
    }
}
