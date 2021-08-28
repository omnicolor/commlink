<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Cyberpunkred;

use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Cyberpunkred\Number;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Tests for rolling dice in Shadowrun 5E.
 * @group discord
 * @group cyberpunkred
 * @group slack
 * @medium
 */
final class NumberTest extends \Tests\TestCase
{
    use \phpmock\phpunit\PHPMock;
    use RefreshDatabase;

    /**
     * Mock random_int function to take randomness out of testing.
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected \PHPUnit\Framework\MockObject\MockObject $randomInt;

    /**
     * Set up the mock random function each time.
     */
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
     * @test
     */
    public function testRollSlack(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $this->randomInt->expects(self::exactly(1))->willReturn(5);
        $response = new Number('5', 'user');
        $response = $response->forSlack($channel);
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
     * @test
     */
    public function testRollSlackCritSuccess(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $this->randomInt
            ->expects(self::exactly(2))
            ->willReturnOnConsecutiveCalls(10, 4);
        $response = new Number('5', 'user');
        $response = $response->forSlack($channel);
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
     * @test
     */
    public function testRollSlackCritFail(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $this->randomInt
            ->expects(self::exactly(2))
            ->willReturnOnConsecutiveCalls(1, 4);
        $response = new Number('5 shooting', 'user');
        $response = $response->forSlack($channel);
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
     * @test
     */
    public function testRollDiscord(): void
    {
        $this->randomInt->expects(self::exactly(1))->willReturn(5);
        $response = new Number('5 perception', 'user');
        $response = $response->forDiscord();
        $expected = "**user made a roll for \"perception\"**\n1d10 + 5 = 5 + 5 = 10";
        self::assertSame($expected, $response);
    }

    /**
     * Test rolling a crit success in Discord.
     * @test
     */
    public function testRollDiscordCritSuccess(): void
    {
        $this->randomInt
            ->expects(self::exactly(2))
            ->willReturnOnConsecutiveCalls(10, 4);
        $response = new Number('5', 'user');
        $response = $response->forDiscord();
        $expected = "**user made a roll with a critical success**\n1d10 + 5 = 10 + 4 + 5 = 19";
        self::assertSame($expected, $response);
    }

    /**
     * Test rolling a crit failure in Discord.
     * @test
     */
    public function testRollDiscordCritFail(): void
    {
        $this->randomInt
            ->expects(self::exactly(2))
            ->willReturnOnConsecutiveCalls(1, 4);
        $response = new Number('5', 'user');
        $response = $response->forDiscord();
        $expected = "**user made a roll with a critical failure**\n1d10 + 5 = 1 - 4 + 5 = 2";
        self::assertSame($expected, $response);
    }
}
