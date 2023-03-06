<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Subversion;

use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Rolls\Subversion\Number;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

/**
 * Tests for rolling dice in Subversion.
 * @group subversion
 * @medium
 */
final class NumberTest extends TestCase
{
    use PHPMock;

    /**
     * Mock random_int function to take randomness out of testing.
     * @var MockObject
     */
    protected MockObject $randomInt;

    /**
     * Set up the mock random function each time.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->randomInt = $this->getFunctionMock(
            'App\\Rolls\\Subversion',
            'random_int'
        );
    }

    /**
     * Test trying to roll a simple three-dice roll without an attribute or TN
     * set in Slack.
     * @test
     */
    public function testSimpleRollSlack(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'subversion']);
        $this->randomInt->expects(self::exactly(3))->willReturn(4);
        $response = json_decode(
            (string)(new Number('3', 'username', $channel))->forSlack()
        );
        self::assertSame(
            'in_channel',
            $response->response_type
        );
        self::assertEquals(
            (object)[
                'color' => SlackResponse::COLOR_INFO,
                'text' => 'Rolled 3 dice for a result of 12',
                'title' => 'username rolled 12',
                'footer' => '4 4 4',
            ],
            $response->attachments[0]
        );
    }

    /**
     * Test trying to roll a simple three-dice roll without the TN set in
     * Discord, but including an attribute.
     * @test
     */
    public function testSimpleRollDiscord(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'subversion']);
        $this->randomInt->expects(self::exactly(4))->willReturn(5);
        $response = (new Number('4', 'username', $channel))->forDiscord();
        self::assertEquals(
            '**username rolled 15**' . \PHP_EOL
                . 'Rolled 4 dice for a result of 15' . \PHP_EOL
                . 'Rolls: 5 5 5 5',
            $response
        );
    }
}
