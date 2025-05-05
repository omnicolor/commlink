<?php

declare(strict_types=1);

namespace Modules\Blistercritters\Tests\Feature\Rolls;

use App\Models\Channel;
use Facades\App\Services\DiceService;
use Modules\Blistercritters\Rolls\Number;
use Omnicolor\Slack\Exceptions\SlackException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use const PHP_EOL;

#[Group('blistercritters')]
#[Medium]
final class NumberTest extends TestCase
{
    #[Group('slack')]
    public function testSlackError(): void
    {
        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'You must include the die size and the target number',
        );

        (new Number('6', 'username', new Channel()))->forSlack();
    }

    #[Group('discord')]
    public function testDiscordError(): void
    {
        self::assertSame(
            'You must include the die size and the target number',
            (new Number('a 6', 'username', new Channel()))->forDiscord(),
        );
    }

    #[Group('irc')]
    public function testIrcError(): void
    {
        self::assertSame(
            'You must include the die size and the target number',
            (new Number('6 a', 'username', new Channel()))->forIrc(),
        );
    }

    #[Group('slack')]
    public function testSlackRollFailure(): void
    {
        DiceService::shouldReceive('rollOne')->once()->with(6)->andReturn(1);
        $response = (new Number('6 5 testing', 'user', new Channel()))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertArrayHasKey('footer', $response['attachments'][0]);
        self::assertSame('danger', $response['attachments'][0]['color']);
        self::assertSame('Rolled 1 vs 5', $response['attachments'][0]['text']);
        self::assertSame('Rolls: 1', $response['attachments'][0]['footer']);
        self::assertSame(
            'user botched the roll for "testing"',
            $response['attachments'][0]['title'],
        );
    }

    #[Group('slack')]
    public function testSlackRollSuccess(): void
    {
        DiceService::shouldReceive('rollOne')->once()->with(6)->andReturn(6);
        $response = (new Number('6 5', 'user', new Channel()))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertArrayHasKey('color', $response['attachments'][0]);
        self::assertSame('good', $response['attachments'][0]['color']);
        self::assertSame('Rolled 6 vs 5', $response['attachments'][0]['text']);
        self::assertSame('user succeeded', $response['attachments'][0]['title']);
    }

    #[Group('discord')]
    public function testDiscordRollWithAdvantage(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(2)
            ->with(8)
            ->andReturn(1, 8);

        $expected = '**user succeeded**' . PHP_EOL
            . 'Rolled 8 vs 6 (advantage)' . PHP_EOL
            . 'Rolls: 1 8';
        $response = new Number('8 6 adv', 'user', new Channel());
        self::assertSame($expected, $response->forDiscord());
    }

    #[Group('irc')]
    public function testIrcRollWithDisadvantage(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(2)
            ->with(8)
            ->andReturn(2, 8);

        $expected = 'user failed' . PHP_EOL
            . 'Rolled 2 vs 6 (disadvantage)' . PHP_EOL
            . 'Rolls: 2 8';
        $response = new Number('8 6 dis', 'user', new Channel());
        self::assertSame($expected, $response->forIrc());
    }
}
