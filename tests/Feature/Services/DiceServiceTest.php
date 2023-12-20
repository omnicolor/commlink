<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use Facades\App\Services\DiceService;
use Illuminate\Foundation\Testing\WithFaker;
use RuntimeException;
use Tests\TestCase;

use function sprintf;

/**
 * @small
 */
final class DiceServiceTest extends TestCase
{
    use WithFaker;

    public function testRollTooFewDice(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('You can\'t roll fewer than 1 die');
        DiceService::rollMany(0, 6);
    }

    public function testRollTooFewPips(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Dice must have more than one pip');
        DiceService::rollMany(1, 1);
    }

    public function testRollOne(): void
    {
        /** @var int<2, 11> */
        $pips = $this->faker->randomDigit() + 2;
        $roll = DiceService::rollOne($pips);
        self::assertGreaterThanOrEqual(1, $roll);
        self::assertLessThanOrEqual($pips, $roll);
    }

    public function testRollMany(): void
    {
        /** @var int<2, 11> */
        $pips = $this->faker->randomDigit() + 2;
        /** @var int<1, 10> */
        $dice = $this->faker->randomDigit() + 1;

        $rolls = DiceService::rollMany($dice, $pips);
        self::assertCount($dice, $rolls);

        foreach ($rolls as $roll) {
            self::assertGreaterThanOrEqual(1, $roll);
            self::assertLessThanOrEqual($pips, $roll);
        }
    }

    public function testGetDynamicPartEmptyString(): void
    {
        self::assertEmpty(DiceService::getDynamicParts(''));
    }

    public function testGetDynamicPartNothingDynamic(): void
    {
        self::assertEmpty(DiceService::getDynamicParts('10+10'));
    }

    public function testGetDynamicPart(): void
    {
        self::assertSame(['9d6'], DiceService::getDynamicParts('10+9d6-27'));
    }

    public function testGetDynamicPartAssumedOne(): void
    {
        self::assertSame(['d6'], DiceService::getDynamicParts('d6'));
    }

    public function testGetDynamicPartAlone(): void
    {
        self::assertSame(['12d12'], DiceService::getDynamicParts('12d12'));
    }

    public function testGetDynamicPartMultiple(): void
    {
        self::assertSame(
            ['2d6', '2d4'],
            DiceService::getDynamicParts('2d6+2d4-5')
        );
    }

    public function testGetLotsOfDynamicParts(): void
    {
        self::assertSame(
            ['d2', '2d4', '4d6', '6d8'],
            DiceService::getDynamicParts('1+d2-2d4+4d6-6d8+10')
        );
    }

    /**
     * @return array<int, array<int, int|string>>
     */
    public static function diceAndPipsProvider(): array
    {
        return [
            ['1d6', 1, 6],
            ['100d4', 100, 4],
            ['d20', 1, 20],
            ['19d100', 19, 100],
            ['1D6', 1, 6],
            ['100D4', 100, 4],
            ['D20', 1, 20],
            ['19D100', 19, 100],
        ];
    }

    /**
     * @dataProvider diceAndPipsProvider
     */
    public function testGetDiceAndPips(
        string $dynamic,
        int $dice,
        int $pips,
    ): void {
        self::assertSame([$dice, $pips], DiceService::getDiceAndPips($dynamic));
    }

    public function testGetDiceAndPipsZeroDice(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('You can\'t roll fewer than 1 die');
        DiceService::getDiceAndPips('0d20');
    }

    public function testGetDiceAndPipsNegativeDice(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('You can\'t roll fewer than 1 die');
        DiceService::getDiceAndPips('-10d20');
    }

    public function testGetDiceAndPipsZeroPips(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Dice must have more than one pip');
        DiceService::getDiceAndPips('10d0');
    }

    public function testGetDiceAndPipsNegativePips(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Dice must have more than one pip');
        DiceService::getDiceAndPips('10d-20');
    }

    public function testRollManyDynamicParts(): void
    {
        $rolls = DiceService::rollDynamic('1+d2-2d4+4d6-6D8+10');
        self::assertArrayHasKey('1d2', $rolls);
        self::assertCount(1, $rolls['1d2']);
        self::assertArrayHasKey('2d4', $rolls);
        self::assertCount(2, $rolls['2d4']);
        self::assertArrayHasKey('4d6', $rolls);
        self::assertCount(4, $rolls['4d6']);
        self::assertArrayHasKey('6d8', $rolls);
        self::assertCount(6, $rolls['6d8']);
    }

    public function testRollDice(): void
    {
        $result = DiceService::rollDice('2d6+1d4+10');
        self::assertCount(3, $result->rolls);
        self::assertSame(array_sum($result->rolls) + 10, $result->total);
        self::assertSame(
            sprintf(
                '2d6+1d4+10 = (%d+%d)+(%d)+10 = %d',
                $result->rolls[0],
                $result->rolls[1],
                $result->rolls[2],
                $result->total,
            ),
            $result->work,
        );
    }

    /** @group current */
    public function testRollMax(): void
    {
        self::assertSame(
            -19,
            DiceService::rollMax('1+d2-2d4+4d6-6d8+10')
        );
    }
}
