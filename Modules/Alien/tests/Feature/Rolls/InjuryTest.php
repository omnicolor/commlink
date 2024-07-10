<?php

declare(strict_types=1);

namespace Modules\Alien\Tests\Feature\Rolls;

use App\Exceptions\SlackException;
use App\Models\Channel;
use Facades\App\Services\DiceService;
use Modules\Alien\Rolls\Injury;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function json_decode;

use const PHP_EOL;

#[Group('alien')]
#[Medium]
final class InjuryTest extends TestCase
{
    #[Group('slack')]
    public function testErrorSlack(): void
    {
        DiceService::shouldReceive('rollOne')->times(2)->with(6)->andReturn(7);
        self::expectException(SlackException::class);
        self::expectExceptionMessage('Injury result (77) was invalid');

        (new Injury('', 'username', new Channel()))->forSlack();
    }

    #[Group('discord')]
    public function testErrorDiscord(): void
    {
        DiceService::shouldReceive('rollOne')->times(2)->with(6)->andReturn(7);

        self::assertSame(
            'Injury result (77) was invalid',
            (new Injury('', 'username', new Channel()))->forDiscord(),
        );
    }

    #[Group('irc')]
    public function testErrorIrc(): void
    {
        DiceService::shouldReceive('rollOne')->times(2)->with(6)->andReturn(7);

        self::assertSame(
            'Injury result (77) was invalid',
            (new Injury('', 'username', new Channel()))->forIrc(),
        );
    }

    #[Group('irc')]
    public function testDeadlyInjury(): void
    {
        DiceService::shouldReceive('rollOne')->times(2)->with(6)->andReturn(5);

        $expected = 'username gains a fatal injury: Severed leg' . PHP_EOL
            . 'Effects: Can\'t run, only crawl. Make a Death Roll after One '
            . 'shift or you will die.' . PHP_EOL . 'Rolls: 5 5';
        self::assertSame(
            $expected,
            (new Injury('', 'username', new Channel()))->forIrc(),
        );
    }

    #[Group('discord')]
    public function testInjuryDiscord(): void
    {
        DiceService::shouldReceive('rollOne')->times(2)->with(6)->andReturn(4);

        $expected = '**username gains a fatal injury: Punctured lung**'
            . PHP_EOL . 'Effects: STAMINA and MOBILITY –2. Make a Death Roll '
            . 'after One day or you will die.' . PHP_EOL . 'Rolls: 4 4';
        self::assertSame(
            $expected,
            (new Injury('', 'username', new Channel()))->forDiscord(),
        );
    }

    #[Group('slack')]
    public function testWeakInjury(): void
    {
        DiceService::shouldReceive('rollOne')->times(2)->with(6)->andReturn(1);
        $response = (new Injury('6', 'user', new Channel()))->forSlack();
        $attachment = json_decode((string)$response)->attachments[0];
        self::assertSame('danger', $attachment->color);
        self::assertSame('Effects: None.', $attachment->text);
        self::assertSame('1 1', $attachment->footer);
        self::assertSame('user gains an injury: Winded', $attachment->title);
    }
}
