<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Cyberpunkred;

use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Cyberpunkred\Number;
use Facades\App\Services\DiceService;
use Tests\TestCase;

/**
 * Tests for rolling dice in Cyberpunk Red.
 * @group cyberpunkred
 * @group discord
 * @group slack
 * @medium
 */
final class NumberTest extends TestCase
{
    /**
     * Test trying to roll.
     * @group slack
     */
    public function testRollSlack(): void
    {
        DiceService::shouldReceive('rollOne')
            ->once()
            ->with(10)
            ->andReturn(5);

        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $channel->username = 'user';
        $response = (new Number('5', 'user', $channel))->forSlack();
        $expected = [
            'response_type' => 'in_channel',
            'attachments' => [
                [
                    'color' => TextAttachment::COLOR_INFO,
                    'footer' => '5',
                    'text' => '1d10 + 5 = 5 + 5 = 10',
                    'title' => 'user made a roll',
                ],
            ],
        ];
        self::assertSame($expected, $response->original);
    }

    /**
     * Test rolling a crit success.
     * @group slack
     */
    public function testRollSlackCritSuccess(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(2)
            ->with(10)
            ->andReturn(10, 4);

        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $channel->username = 'user';
        $response = (new Number('5', 'user', $channel))->forSlack();
        $expected = [
            'response_type' => 'in_channel',
            'attachments' => [
                [
                    'color' => TextAttachment::COLOR_SUCCESS,
                    'footer' => '10 4',
                    'text' => '1d10 + 5 = 10 + 4 + 5 = 19',
                    'title' => 'user made a roll with a critical success',
                ],
            ],
        ];
        self::assertSame($expected, $response->original);
    }

    /**
     * Test rolling a crit failure.
     * @group slack
     */
    public function testRollSlackCritFail(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(2)
            ->with(10)
            ->andReturn(1, 4);

        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $channel->username = 'user';
        $response = (new Number('5 shooting', 'user', $channel))->forSlack();
        $expected = [
            'response_type' => 'in_channel',
            'attachments' => [
                [
                    'color' => TextAttachment::COLOR_DANGER,
                    'footer' => '1 -4',
                    'text' => '1d10 + 5 = 1 - 4 + 5 = 2',
                    'title' => 'user made a roll with a critical failure for "shooting"',
                ],
            ],
        ];
        self::assertSame($expected, $response->original);
    }

    /**
     * Test trying to roll in Discord.
     * @group discord
     */
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
     * @group discord
     */
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
     * @group discord
     */
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
     * @group irc
     */
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
     * @group irc
     */
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
     * @group irc
     */
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
