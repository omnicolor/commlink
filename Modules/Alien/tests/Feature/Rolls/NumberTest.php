<?php

declare(strict_types=1);

namespace Modules\Alient\Tests\Feature\Rolls;

use App\Exceptions\SlackException;
use App\Models\Channel;
use Facades\App\Services\DiceService;
use Modules\Alien\Rolls\Number;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function json_decode;

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
        $response = (new Number('6', 'user', new Channel()))->forSlack();
        $attachment = json_decode((string)$response)->attachments[0];
        self::assertSame('good', $attachment->color);
        self::assertSame('Rolled 6 successes', $attachment->text);
        self::assertSame('6 6 6 6 6 6', $attachment->footer);
        self::assertSame('user succeeded with 6 dice', $attachment->title);
    }

    #[Group('slack')]
    public function testSlackFailure(): void
    {
        DiceService::shouldReceive('rollOne')->times(6)->with(6)->andReturn(1);
        $response = (new Number('6', 'user', new Channel()))->forSlack();
        $attachment = json_decode((string)$response)->attachments[0];
        self::assertSame('danger', $attachment->color);
        self::assertSame('Rolled 0 successes', $attachment->text);
        self::assertSame('1 1 1 1 1 1', $attachment->footer);
        self::assertSame('user failed with 6 dice', $attachment->title);
    }
}
