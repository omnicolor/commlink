<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses\CyberpunkRed;

use App\Exceptions\SlackException;
use App\Http\Responses\Cyberpunkred\NumberResponse;
use App\Models\Channel;

/**
 * Tests for rolling dice in Cyberpunk Red.
 * @covers \App\Http\Responses\CyberpunkRed\NumberResponse
 * @group cyberpunkred
 * @group slack
 * @medium
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
            'App\\Http\\Responses\\Cyberpunkred',
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
            $this->channel = null;
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
     * Test trying to roll without a description.
     * @test
     */
    public function testRollNoDescription(): void
    {
        $this->channel = Channel::factory()->create();
        $this->randomInt->expects(self::any())->willReturn(random_int(1, 10));
        $response = new NumberResponse(
            '5',
            NumberResponse::HTTP_OK,
            [],
            $this->channel
        );
        self::assertStringNotContainsString('for \\"', (string)$response);
    }

    /**
     * Test trying to roll with a description.
     * @test
     */
    public function testRollWithDescription(): void
    {
        $this->channel = Channel::factory()->create();
        $this->randomInt->expects(self::any())->willReturn(random_int(1, 10));
        $response = new NumberResponse(
            '5 description',
            NumberResponse::HTTP_OK,
            [],
            $this->channel
        );
        self::assertStringContainsString(
            'for \\"description\\"',
            (string)$response
        );
    }

    /**
     * Test getting a normal roll.
     * @test
     */
    public function testNormalRoll(): void
    {
        $this->channel = Channel::factory()->create();
        $this->randomInt->expects(self::exactly(1))->willReturn(5);
        $response = new NumberResponse(
            '3',
            NumberResponse::HTTP_OK,
            [],
            $this->channel
        );
        self::assertStringContainsString(
            '1d10 + 3 = 5 + 3 = 8',
            (string)$response
        );
    }

    /**
     * Test getting a critical success roll.
     * @test
     */
    public function testCritSuccess(): void
    {
        $this->channel = Channel::factory()->create();
        $this->randomInt->expects(self::exactly(2))->willReturn(10);
        $response = new NumberResponse(
            '3',
            NumberResponse::HTTP_OK,
            [],
            $this->channel
        );
        self::assertStringContainsString(
            '1d10 + 3 = 10 + 10 + 3 = 23',
            (string)$response
        );
    }

    /**
     * Test getting a critical failure.
     * @test
     */
    public function testCritFailure(): void
    {
        $this->channel = Channel::factory()->create();
        $this->randomInt->expects(self::exactly(2))->willReturn(1);
        $response = new NumberResponse(
            '3',
            NumberResponse::HTTP_OK,
            [],
            $this->channel
        );
        self::assertStringContainsString(
            '1d10 + 3 = 1 - 1 + 3 = 3',
            (string)$response
        );
    }

    /**
     * Test the footer formatting.
     * @test
     */
    public function testFooter(): void
    {
        $this->channel = Channel::factory()->create();
        $this->randomInt->expects(self::exactly(2))->willReturn(10);
        $response = new NumberResponse(
            '3',
            NumberResponse::HTTP_OK,
            [],
            $this->channel
        );
        self::assertStringContainsString('10 10', (string)$response);
    }
}
