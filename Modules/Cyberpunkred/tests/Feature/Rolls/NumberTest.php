<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Tests\Feature\Rolls;

use App\Models\Channel;
use Facades\App\Services\DiceService;
use Modules\Cyberpunkred\Rolls\Number;
use Omnicolor\Slack\Attachment;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('cyberpunkred')]
#[Medium]
final class NumberTest extends TestCase
{
    #[Group('slack')]
    public function testRollSlack(): void
    {
        DiceService::shouldReceive('rollOne')
            ->once()
            ->with(10)
            ->andReturn(5);

        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $channel->username = 'user';
        $response = (new Number('5', 'user', $channel))
            ->forSlack()
            ->jsonSerialize();
        $expected = [
            'blocks' => [],
            'response_type' => 'in_channel',
            'attachments' => [
                [
                    'color' => Attachment::COLOR_INFO,
                    'footer' => '5',
                    'text' => '1d10 + 5 = 5 + 5 = 10',
                    'title' => 'user made a roll',
                ],
            ],
        ];
        self::assertSame($expected, $response);
    }

    /**
     * Test rolling a crit success.
     */
    #[Group('slack')]
    public function testRollSlackCritSuccess(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(2)
            ->with(10)
            ->andReturn(10, 4);

        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $channel->username = 'user';
        $response = (new Number('5', 'user', $channel))
            ->forSlack()
            ->jsonSerialize();
        $expected = [
            'blocks' => [],
            'response_type' => 'in_channel',
            'attachments' => [
                [
                    'color' => Attachment::COLOR_SUCCESS,
                    'footer' => '10 4',
                    'text' => '1d10 + 5 = 10 + 4 + 5 = 19',
                    'title' => 'user made a roll with a critical success',
                ],
            ],
        ];
        self::assertSame($expected, $response);
    }

    /**
     * Test rolling a crit failure.
     */
    #[Group('slack')]
    public function testRollSlackCritFail(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(2)
            ->with(10)
            ->andReturn(1, 4);

        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $channel->username = 'user';
        $response = (new Number('5 shooting', 'user', $channel))
            ->forSlack()
            ->jsonSerialize();
        $expected = [
            'blocks' => [],
            'response_type' => 'in_channel',
            'attachments' => [
                [
                    'color' => Attachment::COLOR_DANGER,
                    'footer' => '1 -4',
                    'text' => '1d10 + 5 = 1 - 4 + 5 = 2',
                    'title' => 'user made a roll with a critical failure for "shooting"',
                ],
            ],
        ];
        self::assertSame($expected, $response);
    }

    #[Group('discord')]
    public function testRollDiscord(): void
    {
        DiceService::shouldReceive('rollOne')->once()->with(10)->andReturn(5);

        $channel = new Channel();
        $channel->username = 'user';
        $response = (new Number('5 perception', 'user', $channel))
            ->forDiscord();
        $expected = "**user made a roll for \"perception\"**\n1d10 + 5 = 5 + 5 = 10";
        self::assertSame($expected, $response);
    }

    /**
     * Test rolling a crit success in Discord.
     */
    #[Group('discord')]
    public function testRollDiscordCritSuccess(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(2)
            ->with(10)
            ->andReturn(10, 4);

        $channel = new Channel();
        $channel->username = 'user';
        $response = (new Number('5', 'user', $channel))->forDiscord();
        $expected = "**user made a roll with a critical success**\n1d10 + 5 = 10 + 4 + 5 = 19";
        self::assertSame($expected, $response);
    }

    /**
     * Test rolling a crit failure in Discord.
     */
    #[Group('discord')]
    public function testRollDiscordCritFail(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(2)
            ->with(10)
            ->andReturn(1, 4);

        $response = (new Number('5', 'user', new Channel()))->forDiscord();
        $expected = "**user made a roll with a critical failure**\n1d10 + 5 = 1 - 4 + 5 = 2";
        self::assertSame($expected, $response);
    }

    /**
     * Test trying to roll in IRC.
     */
    #[Group('irc')]
    public function testRollIRC(): void
    {
        DiceService::shouldReceive('rollOne')
            ->once()
            ->with(10)
            ->andReturn(5);

        $channel = new Channel();
        $channel->username = 'user';
        $response = (new Number('5 perception', 'user', $channel))->forIrc();
        $expected = "user made a roll for \"perception\"\n1d10 + 5 = 5 + 5 = 10";
        self::assertSame($expected, $response);
    }

    /**
     * Test rolling a crit success in IRC.
     */
    #[Group('irc')]
    public function testRollIRCCritSuccess(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(2)
            ->with(10)
            ->andReturn(10, 4);

        $channel = new Channel();
        $channel->username = 'user';
        $response = (new Number('5', 'user', $channel))->forIrc();
        $expected = "user made a roll with a critical success\n1d10 + 5 = 10 + 4 + 5 = 19";
        self::assertSame($expected, $response);
    }

    /**
     * Test rolling a crit failure in IRC.
     */
    #[Group('irc')]
    public function testRollIRCCritFail(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(2)
            ->with(10)
            ->andReturn(1, 4);

        $response = (new Number('5', 'user', new Channel()))->forIrc();
        $expected = "user made a roll with a critical failure\n1d10 + 5 = 1 - 4 + 5 = 2";
        self::assertSame($expected, $response);
    }
}
