<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Cyberpunkred;

use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Cyberpunkred\Number;
use Illuminate\Foundation\Testing\RefreshDatabase;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\MockObject\MockObject;
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
    use PHPMock;
    use RefreshDatabase;

    protected MockObject $randomInt;

    public function setUp(): void
    {
        parent::setUp();
        $this->randomInt = $this->getFunctionMock(
            'App\\Rolls\\Cyberpunkred',
            'random_int'
        );
    }

    /**
     * Test trying to roll.
     * @group slack
     * @test
     */
    public function testRollSlack(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $channel->username = 'user';
        $this->randomInt->expects(self::exactly(1))->willReturn(5);
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
     * @test
     */
    public function testRollSlackCritSuccess(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $channel->username = 'user';
        $this->randomInt
            ->expects(self::exactly(2))
            ->willReturnOnConsecutiveCalls(10, 4);
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
     * @test
     */
    public function testRollSlackCritFail(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $channel->username = 'user';
        $this->randomInt
            ->expects(self::exactly(2))
            ->willReturnOnConsecutiveCalls(1, 4);
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
     * @test
     */
    public function testRollDiscord(): void
    {
        $channel = new Channel();
        $channel->username = 'user';
        $this->randomInt->expects(self::exactly(1))->willReturn(5);
        $response = (new Number('5 perception', 'user', $channel))
            ->forDiscord();
        $expected = "**user made a roll for \"perception\"**\n1d10 + 5 = 5 + 5 = 10";
        self::assertSame($expected, $response);
    }

    /**
     * Test rolling a crit success in Discord.
     * @group discord
     * @test
     */
    public function testRollDiscordCritSuccess(): void
    {
        $channel = new Channel();
        $channel->username = 'user';
        $this->randomInt
            ->expects(self::exactly(2))
            ->willReturnOnConsecutiveCalls(10, 4);
        $response = (new Number('5', 'user', $channel))->forDiscord();
        $expected = "**user made a roll with a critical success**\n1d10 + 5 = 10 + 4 + 5 = 19";
        self::assertSame($expected, $response);
    }

    /**
     * Test rolling a crit failure in Discord.
     * @group discord
     * @test
     */
    public function testRollDiscordCritFail(): void
    {
        $this->randomInt
            ->expects(self::exactly(2))
            ->willReturnOnConsecutiveCalls(1, 4);
        $response = (new Number('5', 'user', new Channel()))->forDiscord();
        $expected = "**user made a roll with a critical failure**\n1d10 + 5 = 1 - 4 + 5 = 2";
        self::assertSame($expected, $response);
    }

    /**
     * Test trying to roll in IRC.
     * @group irc
     * @test
     */
    public function testRollIRC(): void
    {
        $channel = new Channel();
        $channel->username = 'user';
        $this->randomInt->expects(self::exactly(1))->willReturn(5);
        $response = (new Number('5 perception', 'user', $channel))->forIrc();
        $expected = "user made a roll for \"perception\"\n1d10 + 5 = 5 + 5 = 10";
        self::assertSame($expected, $response);
    }

    /**
     * Test rolling a crit success in IRC.
     * @group irc
     * @test
     */
    public function testRollIRCCritSuccess(): void
    {
        $channel = new Channel();
        $channel->username = 'user';
        $this->randomInt
            ->expects(self::exactly(2))
            ->willReturnOnConsecutiveCalls(10, 4);
        $response = (new Number('5', 'user', $channel))->forIrc();
        $expected = "user made a roll with a critical success\n1d10 + 5 = 10 + 4 + 5 = 19";
        self::assertSame($expected, $response);
    }

    /**
     * Test rolling a crit failure in IRC.
     * @group irc
     * @test
     */
    public function testRollIRCCritFail(): void
    {
        $this->randomInt
            ->expects(self::exactly(2))
            ->willReturnOnConsecutiveCalls(1, 4);
        $response = (new Number('5', 'user', new Channel()))->forIrc();
        $expected = "user made a roll with a critical failure\n1d10 + 5 = 1 - 4 + 5 = 2";
        self::assertSame($expected, $response);
    }
}
