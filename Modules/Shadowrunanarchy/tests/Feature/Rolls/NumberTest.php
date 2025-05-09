<?php

declare(strict_types=1);

namespace Modules\Shadowrunanarchy\Tests\Feature\Rolls;

use App\Models\Channel;
use Facades\App\Services\DiceService;
use Modules\Shadowrunanarchy\Rolls\Number;
use Omnicolor\Slack\Exceptions\SlackException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use const PHP_EOL;

#[Group('shadowrunanarchy')]
#[Medium]
final class NumberTest extends TestCase
{
    #[Group('slack')]
    public function testRollTooManySlack(): void
    {
        self::expectException(SlackException::class);
        self::expectExceptionMessage('You can\'t roll more than 100 dice!');
        (new Number('101', 'username', new Channel()))->forSlack();
    }

    #[Group('discord')]
    public function testRollTooManyDiscord(): void
    {
        $result = (new Number('101', 'user', new Channel()))->forDiscord();
        self::assertSame('You can\'t roll more than 100 dice!', $result);
    }

    #[Group('irc')]
    public function testRollTooManyIrc(): void
    {
        $result = (new Number('101', 'user', new Channel()))->forIrc();
        self::assertSame('You can\'t roll more than 100 dice!', $result);
    }

    #[Group('slack')]
    public function testSuccessSlack(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(6)
            ->with(6)
            ->andReturn(6);
        $result = (new Number('6', 'user', new Channel()))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $result);
        self::assertSame(
            [
                'color' => 'good',
                'footer' => '6 6 6 6 6 6',
                'text' => '6 dice',
                'title' => 'user rolled 6 successes',
            ],
            $result['attachments'][0],
        );
    }

    #[Group('slack')]
    public function testFailureWithDescriptionSlack(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(6)
            ->with(6)
            ->andReturn(1);
        $result = (new Number('6 testing', 'user', new Channel()))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $result);
        self::assertSame(
            [
                'color' => 'danger',
                'footer' => '1 1 1 1 1 1',
                'text' => '6 dice',
                'title' => 'user rolled 0 successes for "testing"',
            ],
            $result['attachments'][0],
        );
    }

    #[Group('discord')]
    public function testGlitchDieNoEffect(): void
    {
        DiceService::shouldReceive('rollOne')->times(9)->with(6)->andReturn(3);
        $result = (new Number('8 glitch', 'user', new Channel()))->forDiscord();
        self::assertSame(
            '**user rolled 0 successes**' . PHP_EOL
                . '8 dice plus a glitch die' . PHP_EOL
                . 'Rolls: 3 3 3 3 3 3 3 3 3',
            $result,
        );
    }

    #[Group('irc')]
    public function testGlitchDieWithGlitch(): void
    {
        DiceService::shouldReceive('rollOne')->times(5)->with(6)->andReturn(1);
        $result = (new Number('4 glitch', 'user', new Channel()))->forIrc();
        self::assertSame(
            'user rolled 0 successes, GLITCHED' . PHP_EOL
                . '4 dice plus a glitch die' . PHP_EOL
                . 'Rolls: 1 1 1 1 1',
            $result,
        );
    }

    #[Group('slack')]
    public function testGlitchDieWithExploit(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(6)
            ->with(6)
            ->andReturn(6);
        $result = (new Number('5 glitch', 'user', new Channel()))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $result);
        self::assertSame(
            [
                'color' => 'good',
                'footer' => '6 6 6 6 6 6',
                'text' => '5 dice plus a glitch die',
                'title' => 'user rolled 5 successes, EXPLOITED',
            ],
            $result['attachments'][0],
        );
    }
}
