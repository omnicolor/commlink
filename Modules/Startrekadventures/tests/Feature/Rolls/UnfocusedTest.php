<?php

declare(strict_types=1);

namespace Modules\Startrekadventures\Tests\Feature\Rolls;

use App\Models\Channel;
use Facades\App\Services\DiceService;
use Modules\Startrekadventures\Rolls\Unfocused;
use Omnicolor\Slack\Attachment;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use const PHP_EOL;

#[Group('startrekadventures')]
#[Medium]
final class UnfocusedTest extends TestCase
{
    #[Group('slack')]
    public function testUnfocusedSlack(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(2)
            ->with(20)
            ->andReturn(3);

        $response = (new Unfocused(
            'unfocused 1 2 3',
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
                'title' => 'username failed a roll without a focus',
            ],
            $response['attachments'][0],
        );
    }

    /**
     * Test making an unfocused roll with extra dice.
     */
    #[Group('discord')]
    public function testUnfocusedExtraDice(): void
    {
        $channel = Channel::factory()->make();

        DiceService::shouldReceive('rollOne')->times(6)->with(20)->andReturn(3);

        $response = (new Unfocused('unfocused 1 2 3 4', 'username', $channel))
            ->forDiscord();

        $expected = '**username succeeded without a focus**' . PHP_EOL
            . 'Rolled 6 successes' . PHP_EOL . 'Rolls: 3 3 3 3 3 3';
        self::assertSame($expected, $response);
    }

    /**
     * Test making an unfocused roll resulting in a complication.
     */
    #[Group('discord')]
    public function testUnfocusedWithComplication(): void
    {
        $channel = Channel::factory()->make();

        DiceService::shouldReceive('rollOne')
            ->times(2)
            ->with(20)
            ->andReturn(20);

        $response = (new Unfocused('unfocused 1 2 3', 'username', $channel))
            ->forDiscord();

        $expected = '**username failed a roll without a focus**' . PHP_EOL
            . 'Rolled 0 successes with 2 complications' . PHP_EOL
            . 'Rolls: 20 20';
        self::assertSame($expected, $response);
    }

    /**
     * Test getting extra successes with natural ones.
     */
    #[Group('discord')]
    public function testUnfocusedNaturalOnes(): void
    {
        DiceService::shouldReceive('rollOne')->times(2)->with(20)->andReturn(1);

        $channel = Channel::factory()->make();

        $response = (new Unfocused('unfocused 1 2 3', 'username', $channel))
            ->forDiscord();

        $expected = '**username succeeded without a focus**' . PHP_EOL
            . 'Rolled 4 successes' . PHP_EOL
            . 'Rolls: 1 1';
        self::assertSame($expected, $response);
    }

    /**
     * Test making an unfocused roll with optional text.
     */
    #[Group('slack')]
    public function testUnfocusedRollWithOptionalText(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(2)
            ->with(20)
            ->andReturn(3);

        $response = (new Unfocused(
            'unfocused 1 2 3 testing',
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
                'title' => 'username failed a roll without a focus for "testing"',
            ],
            $response['attachments'][0],
        );
    }

    #[Group('irc')]
    public function testUnfocusedIrc(): void
    {
        DiceService::shouldReceive('rollOne')->times(2)->with(20)->andReturn(1);

        $channel = Channel::factory()->make();

        $response = (new Unfocused('unfocused 1 2 3', 'username', $channel))
            ->forIrc();

        $expected = 'username succeeded without a focus' . PHP_EOL
            . 'Rolled 4 successes' . PHP_EOL
            . 'Rolls: 1 1';
        self::assertSame($expected, $response);
    }
}
