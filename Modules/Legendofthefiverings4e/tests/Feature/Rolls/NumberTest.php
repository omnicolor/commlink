<?php

declare(strict_types=1);

namespace Modules\Legendofthefiverings4e\Tests\Feature\Rolls;

use App\Exceptions\SlackException;
use App\Models\Channel;
use Facades\App\Services\DiceService;
use Modules\Legendofthefiverings4e\Rolls\Number;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function json_decode;

use const PHP_EOL;

#[Group('legendofthefiverings4e')]
#[Medium]
final class NumberTest extends TestCase
{
    #[Group('slack')]
    public function testTooFewArgumentsError(): void
    {
        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'LotFR rolls require two numbers: how many dice to roll and how '
                . 'many to keep.',
        );
        (new Number('', 'user', new Channel()))->forSlack();
    }

    #[Group('discord')]
    public function testNonNumericNumberOfDice(): void
    {
        self::assertSame(
            'LotFR rolls require two numbers: how many dice to roll and how '
                . 'many to keep.',
            (new Number('a 5', 'user', new Channel()))->forDiscord(),
        );
    }

    #[Group('irc')]
    public function testNonNumericNumberToKeep(): void
    {
        self::assertSame(
            'LotFR rolls require two numbers: how many dice to roll and how '
                . 'many to keep.',
            (new Number('5 a', 'user', new Channel()))->forIrc(),
        );
    }

    #[Group('slack')]
    public function testRollingWithoutExploding(): void
    {
        DiceService::shouldReceive('rollOne')->times(6)->with(10)->andReturn(3);
        $response = (new Number('6 3 testing', 'user', new Channel()))
            ->forSlack();
        $response = json_decode((string)$response);
        $response = $response->attachments[0];
        self::assertSame(
            'user rolled 9 for "testing"',
            $response->title,
        );
        self::assertSame('Rolled 6, kept 3', $response->text);
        self::assertSame('good', $response->color);
        self::assertSame('3 3 3 3 3 3', $response->footer);
    }

    #[Group('discord')]
    public function testRollExplode(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(6)
            ->with(10)
            ->andReturn(10, 9, 8, 7, 6, 5);
        $response = (new Number('5 3', 'user', new Channel()))->forDiscord();
        self::assertSame(
            '**user rolled 34**' . PHP_EOL
                . 'Rolled 5, kept 3' . PHP_EOL
                . 'Rolls: 19 8 7 6 5',
            $response,
        );
    }

    #[Group('irc')]
    public function testRollIrc(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(5)
            ->with(10)
            ->andReturn(9, 9, 8, 7, 6);
        $response = (new Number('5 3', 'user', new Channel()))->forIrc();
        self::assertSame(
            'user rolled 26' . PHP_EOL
                . 'Rolled 5, kept 3' . PHP_EOL
                . 'Rolls: 9 9 8 7 6',
            $response,
        );
    }
}