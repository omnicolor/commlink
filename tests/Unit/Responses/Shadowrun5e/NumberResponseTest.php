<?php

declare(strict_types=1);

namespace Tests\Unit\Responses\Shadowrun5e;

use App\Exceptions\SlackException;
use App\Http\Responses\Shadowrun5e\NumberResponse;
use App\Models\Slack\Channel;

/**
 * Tests for rolling dice in Shadowrun 5E.
 * @covers \App\Http\Responses\Shadowrun5e\NumberResponse
 * @group shadowrun
 * @group shadowrun5e
 * @group slack
 */
final class NumberResponseTest extends \Tests\TestCase
{
    use \phpmock\phpunit\PHPMock;

    /**
     * Channel used for testing.
     * @var ?Channel
     */
    protected ?Channel $channel;

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
            'App\\Http\\Responses\\Shadowrun5e',
            'random_int'
        );
    }

    /**
     * Clean up after the tests.
     */
    public function tearDown(): void
    {
        if (isset($this->channel)) {
            $this->channel->delete();
            unset($this->channel);
        }
        parent::tearDown();
    }

    /**
     * Test not including a channel.
     * @test
     */
    public function testNoChannel(): void
    {
        self::expectException(SlackException::class);
        self::expectExceptionMessage('Channel is required');
        $this->randomInt->expects(self::never());
        new NumberResponse('5', NumberResponse::HTTP_OK, []);
    }

    /**
     * Test trying to roll without a limit or description.
     * @test
     */
    public function testRollNoLimitNoDescription(): void
    {
        $this->channel = Channel::factory()->create();
        $this->randomInt->expects(self::any())->willReturn(random_int(1, 6));
        $response = new NumberResponse(
            '5',
            NumberResponse::HTTP_OK,
            [],
            $this->channel
        );
        self::assertStringNotContainsString('limit', (string)$response);
        self::assertStringNotContainsString('for', (string)$response);
    }

    /**
     * Test trying to roll with a limit.
     * @test
     */
    public function testRollWithLimit(): void
    {
        $this->channel = Channel::factory()->create();
        $this->randomInt->expects(self::any())->willReturn(random_int(1, 6));
        $response = new NumberResponse(
            '15 5',
            NumberResponse::HTTP_OK,
            [],
            $this->channel
        );
        self::assertStringContainsString(', limit: 5', (string)$response);
        self::assertStringNotContainsString('for', (string)$response);
    }

    /**
     * Test trying to roll with a description.
     * @test
     */
    public function testRollWithDescription(): void
    {
        $this->channel = Channel::factory()->create();
        $this->randomInt->expects(self::any())->willReturn(random_int(1, 6));
        $response = new NumberResponse(
            '5 description',
            NumberResponse::HTTP_OK,
            [],
            $this->channel
        );
        self::assertStringNotContainsString('limit', (string)$response);
        self::assertStringContainsString(
            'for \\"description\\"',
            (string)$response
        );
    }

    /**
     * Test trying to roll with both a description and a limit.
     * @test
     */
    public function testRollBoth(): void
    {
        $this->channel = Channel::factory()->create();
        $this->randomInt->expects(self::any())->willReturn(random_int(1, 6));
        $response = new NumberResponse(
            '20 10 description',
            NumberResponse::HTTP_OK,
            [],
            $this->channel
        );
        self::assertStringContainsString('limit: 10', (string)$response);
        self::assertStringContainsString(
            'for \\"description\\"',
            (string)$response
        );
    }

    /**
     * Test trying to roll too many dice.
     * @test
     */
    public function testRollTooMany(): void
    {
        self::expectException(SlackException::class);
        self::expectExceptionMessage('You can\'t roll more than 100 dice');
        $this->randomInt->expects(self::never());
        new NumberResponse('101', NumberResponse::HTTP_OK, [], new Channel());
    }

    /**
     * Test the user rolling a critical glitch.
     * @test
     */
    public function testCriticalGlitch(): void
    {
        $this->channel = Channel::factory()->create();
        $this->channel->username = 'Bob';
        $this->randomInt->expects(self::exactly(3))->willReturn(1);
        $response = new NumberResponse(
            '3',
            NumberResponse::HTTP_OK,
            [],
            $this->channel
        );
        self::assertStringContainsString(
            'rolled a critical glitch on 3 dice!',
            (string)$response
        );
    }

    /**
     * Test the footer formatting a user getting successes.
     * @test
     */
    public function testFooterSixes(): void
    {
        $this->channel = Channel::factory()->create();
        $this->randomInt->expects(self::exactly(3))->willReturn(6);
        $response = new NumberResponse(
            '3',
            NumberResponse::HTTP_OK,
            [],
            $this->channel
        );
        self::assertStringContainsString(
            '*6* *6* *6*',
            (string)$response
        );
    }

    /**
     * Test the description when the roll hits the limit.
     * @test
     */
    public function testDescriptionHitLimit(): void
    {
        $this->channel = Channel::factory()->create();
        $this->randomInt->expects(self::exactly(6))->willReturn(5);
        $response = new NumberResponse(
            '6 3 shooting',
            NumberResponse::HTTP_OK,
            [],
            $this->channel
        );
        self::assertStringContainsString(
            'Rolled 3 successes for \\"shooting\\", hit limit',
            (string)$response
        );
    }
}
