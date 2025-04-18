<?php

declare(strict_types=1);

namespace Modules\Alien\Tests\Feature\Rolls;

use App\Models\Channel;
use Facades\App\Services\DiceService;
use Modules\Alien\Rolls\Number;
use Omnicolor\Slack\Attachment;
use Omnicolor\Slack\Exceptions\SlackException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use const PHP_EOL;

#[Group('alien')]
#[Medium]
final class NumberTest extends TestCase
{
    #[Group('slack')]
    public function testRollTooManySlack(): void
    {
        self::expectException(SlackException::class);
        self::expectExceptionMessage('You can\'t roll more than 20 dice!');

        (new Number('21', 'username', new Channel()))->forSlack();
    }

    #[Group('discord')]
    public function testRollTooManyDiscord(): void
    {
        $response = new Number('101', 'Loftwyr', new Channel());
        self::assertSame(
            'You can\'t roll more than 20 dice!',
            $response->forDiscord(),
        );
    }

    #[Group('irc')]
    public function testRollTooManyIrc(): void
    {
        $response = new Number('101', 'Loftwyr', new Channel());
        self::assertSame(
            'You can\'t roll more than 20 dice!',
            $response->forIrc(),
        );
    }

    #[Group('discord')]
    public function testSuccess(): void
    {
        DiceService::shouldReceive('rollOne')->times(1)->with(6)->andReturn(6);

        $expected = '**username succeeded with 1 die**' . PHP_EOL
            . 'Rolled 1 success' . PHP_EOL
            . 'Rolls: 6';
        $response = new Number('1', 'username', new Channel());
        self::assertSame($expected, $response->forDiscord());
    }

    #[Group('irc')]
    public function testIrcSuccess(): void
    {
        DiceService::shouldReceive('rollOne')->times(2)->with(6)->andReturn(6);

        $expected = 'username succeeded with 2 dice' . PHP_EOL
            . 'Rolled 2 successes' . PHP_EOL
            . 'Rolls: 6 6';
        $response = new Number('2', 'username', new Channel());
        self::assertSame($expected, $response->forIrc());
    }

    #[Group('slack')]
    public function testSlackSuccess(): void
    {
        DiceService::shouldReceive('rollOne')->times(6)->with(6)->andReturn(6);
        $response = (new Number('6', 'user', new Channel()))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertArrayHasKey('color', $response['attachments'][0]);
        self::assertArrayHasKey('footer', $response['attachments'][0]);
        self::assertSame(
            Attachment::COLOR_SUCCESS,
            $response['attachments'][0]['color'],
        );
        self::assertSame('Rolled 6 successes', $response['attachments'][0]['text']);
        self::assertSame('6 6 6 6 6 6', $response['attachments'][0]['footer']);
        self::assertSame('user succeeded with 6 dice', $response['attachments'][0]['title']);
    }

    #[Group('slack')]
    public function testSlackFailure(): void
    {
        DiceService::shouldReceive('rollOne')->times(6)->with(6)->andReturn(1);
        $response = (new Number('6', 'user', new Channel()))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertArrayHasKey('color', $response['attachments'][0]);
        self::assertArrayHasKey('footer', $response['attachments'][0]);
        self::assertSame(
            Attachment::COLOR_DANGER,
            $response['attachments'][0]['color'],
        );
        self::assertSame('Rolled 0 successes', $response['attachments'][0]['text']);
        self::assertSame('1 1 1 1 1 1', $response['attachments'][0]['footer']);
        self::assertSame('user failed with 6 dice', $response['attachments'][0]['title']);
    }

    #[Group('discord')]
    public function testPanicWithFailure(): void
    {
        DiceService::shouldReceive('rollOne')->times(6)->with(6)->andReturn(1);
        $response = (new Number('5 1 shooting', 'user', new Channel()))
            ->forDiscord();
        $expected = '**user failed with 6 dice and panics for "shooting"**'
            . PHP_EOL . 'Rolled 0 successes' . PHP_EOL . 'Rolls: 1 1 1 1 1 1';
        self::assertSame($expected, $response);
    }

    #[Group('discord')]
    public function testPanicWithSuccess(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(6)
            ->with(6)
            ->andReturn(6, 1, 3, 3, 6, 1);
        $response = (new Number('4 2', 'user', new Channel()))
            ->forDiscord();
        $expected = '**user succeeded, but panics with 6 dice**'
            . PHP_EOL . 'Rolled 2 successes' . PHP_EOL . 'Rolls: 6 1 3 3 6 1';
        self::assertSame($expected, $response);
    }
}
