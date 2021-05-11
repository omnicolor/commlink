<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses\Expanse;

use App\Exceptions\SlackException;
use App\Http\Responses\Expanse\NumberResponse;
use App\Models\Channel;

/**
 * Tests for rolling dice in The Expanse.
 * @covers \App\Http\Responses\Expanse\NumberResponse
 * @group expanse
 * @group slack
 * @small
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
            'App\\Http\\Responses\\Expanse',
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
        $this->randomInt->expects(self::any())->willReturn(random_int(1, 6));
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
        $this->randomInt->expects(self::any())->willReturn(random_int(1, 6));
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
     * Test the rolls generating stunt points.
     * @test
     */
    public function testStuntPoints(): void
    {
        $this->channel = Channel::factory()->create();
        $this->randomInt->expects(self::any())->willReturn(3);
        $response = new NumberResponse(
            '5',
            NumberResponse::HTTP_OK,
            [],
            $this->channel
        );
        self::assertStringContainsString('14 (3 SP)', (string)$response);
    }

    /**
     * Test the rolls not generating any stunt points.
     * @test
     */
    public function testNoStuntPoints(): void
    {
        $this->channel = Channel::factory()->create();
        $this->randomInt->expects(self::any())
            ->willReturn(self::onConsecutiveCalls(2, 3, 5));
        $response = new NumberResponse(
            '5',
            NumberResponse::HTTP_OK,
            [],
            $this->channel
        );
        self::assertStringNotContainsString('SP)', (string)$response);
    }

    /**
     * Test the footer formatting.
     * @test
     */
    public function testFooter(): void
    {
        $this->channel = Channel::factory()->create();
        $this->randomInt->expects(self::exactly(3))->willReturn(6);
        $response = new NumberResponse(
            '3',
            NumberResponse::HTTP_OK,
            [],
            $this->channel
        );
        self::assertStringContainsString('6 6 `6`', (string)$response);
    }
}
