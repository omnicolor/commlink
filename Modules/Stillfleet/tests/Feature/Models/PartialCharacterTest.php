<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Tests\Feature\Models;

use Iterator;
use Modules\Stillfleet\Models\PartialCharacter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('stillfleet')]
#[Small]
final class PartialCharacterTest extends TestCase
{
    public function testToCharacter(): void
    {
        $partialCharacter = new PartialCharacter(['_id' => 'deadbeef']);
        $character = $partialCharacter->toCharacter();
        self::assertNull($character->_id);
    }

    public function testStartingMoneyNoCharm(): void
    {
        $partialCharacter = new PartialCharacter(['will' => 'd4']);
        self::expectException(RuntimeException::class);
        $partialCharacter->startingMoney();
    }

    public function testStartingMoneyWithNoWill(): void
    {
        $partialCharacter = new PartialCharacter(['charm' => 'd4']);
        self::expectException(RuntimeException::class);
        $partialCharacter->startingMoney();
    }

    /**
     * @return Iterator<int, array<int, (int | string)>>
     */
    public static function startingMoneyAttributeProvider(): Iterator
    {
        yield ['d12', 'd4', 160];
        yield ['d10', 'd6', 160];
        yield ['d6', 'd6', 120];
        yield ['d4', 'd4', 80];
    }

    #[DataProvider('startingMoneyAttributeProvider')]
    public function testStartingMoneyAttributes(string $will, string $charm, int $expected): void
    {
        $character = new PartialCharacter(['charm' => $charm, 'will' => $will]);
        self::assertSame($expected, $character->startingMoney());
    }
}
