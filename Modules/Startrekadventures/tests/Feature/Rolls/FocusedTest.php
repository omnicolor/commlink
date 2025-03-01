<?php

declare(strict_types=1);

namespace Modules\Startrekadventures\Tests\Feature\Rolls;

use App\Models\Channel;
use Facades\App\Services\DiceService;
use Modules\Startrekadventures\Rolls\Focused;
use Omnicolor\Slack\Attachment;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use const PHP_EOL;

#[Group('startrekadventures')]
#[Medium]
final class FocusedTest extends TestCase
{
    #[Group('slack')]
    public function testFocusedSlack(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(2)
            ->with(20)
            ->andReturn(3);

        $response = (new Focused(
            'focused 1 2 3',
            'username',
            Channel::factory()->make(),
        ))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertSame(
            [
                'color' => Attachment::COLOR_DANGER,
                'footer' => 'Rolls: 3 3',
                'text' => 'Rolled 2 successes',
                'title' => 'username failed a roll with a focus',
            ],
            $response['attachments'][0],
        );
    }

    #[Group('discord')]
    public function testFocusedExtraDice(): void
    {
        DiceService::shouldReceive('rollOne')->times(6)->with(20)->andReturn(3);

        $channel = Channel::factory()->make();
        $response = (new Focused('focused 1 2 3 4', 'username', $channel))
            ->forDiscord();

        $expected = '**username succeeded with a focus**' . PHP_EOL
            . 'Rolled 6 successes' . PHP_EOL . 'Rolls: 3 3 3 3 3 3';
        self::assertSame($expected, $response);
    }

    #[Group('irc')]
    public function testFocusedWithComplication(): void
    {
        DiceService::shouldReceive('rollOne')->times(2)->with(20)->andReturn(20);

        $channel = Channel::factory()->make();
        $response = (new Focused('focused 1 2 3', 'username', $channel))
            ->forIrc();

        $expected = 'username failed a roll with a focus' . PHP_EOL
            . 'Rolled 0 successes with 2 complications' . PHP_EOL
            . 'Rolls: 20 20';
        self::assertSame($expected, $response);
    }

    #[Group('discord')]
    public function testFocusedNaturalOnes(): void
    {
        DiceService::shouldReceive('rollOne')->times(2)->with(20)->andReturn(1);

        $channel = Channel::factory()->make();
        $response = (new Focused('focused 1 2 3', 'username', $channel))
            ->forDiscord();

        $expected = '**username succeeded with a focus**' . PHP_EOL
            . 'Rolled 4 successes' . PHP_EOL
            . 'Rolls: 1 1';
        self::assertSame($expected, $response);
    }

    #[Group('slack')]
    public function testFocusedRollWithOptionalText(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(2)
            ->with(20)
            ->andReturn(3);

        $response = (new Focused(
            'focused 1 2 3 testing',
            'username',
            Channel::factory()->make(),
        ))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertSame(
            [
                'color' => Attachment::COLOR_DANGER,
                'footer' => 'Rolls: 3 3',
                'text' => 'Rolled 2 successes',
                'title' => 'username failed a roll with a focus for "testing"',
            ],
            $response['attachments'][0],
        );
    }
}
