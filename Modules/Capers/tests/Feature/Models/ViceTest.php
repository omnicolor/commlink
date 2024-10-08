<?php

declare(strict_types=1);

namespace Modules\Capers\Tests\Feature\Models;

use App\Models\Card;
use Modules\Capers\Models\Vice;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('capers')]
#[Small]
final class ViceTest extends TestCase
{
    /**
     * Test loading an invalid vice.
     */
    public function testInvalidVice(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Vice ID "invalid" is invalid');
        new Vice('invalid');
    }

    /**
     * Test loading a vice.
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
     */
    public function testAll(): void
    {
        $vices = Vice::all();
        self::assertNotEmpty($vices);
        self::assertSame('Vain', (string)$vices['vain']);
    }

    /**
     * Test trying to find a vice for an invalid card.
     */
    public function testFindForInvalidCard(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Vice not found for 30☃');
        Vice::findForCard(new Card('30', '☃'));
    }

    /**
     * Test trying to find a vice for a card.
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
