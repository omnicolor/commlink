<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Tests\Feature\Models;

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
     * @return array<int, array<int, int|string>>
     */
    public static function startingMoneyAttributeProvider(): array
    {
        return [
            ['d12', 'd4', 1600],
            ['d10', 'd6', 1600],
            ['d6', 'd6', 1200],
            ['d4', 'd4', 800],
        ];
    }

    #[DataProvider('startingMoneyAttributeProvider')]
    public function testStartingMoneyAttributes(string $will, string $charm, int $expected): void
    {
        $character = new PartialCharacter(['charm' => $charm, 'will' => $will]);
        self::assertEquals($expected, $character->startingMoney());
    }
}
