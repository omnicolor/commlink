<?php

declare(strict_types=1);

namespace Tests\Unit\Responses;

use App\Exceptions\SlackException;
use App\Http\Responses\GenericRollResponse;
use App\Models\Slack\Channel;

/**
 * Tests for rolling generic dice.
 * @covers \App\Http\Responses\GenericRollResponse
 * @group slack
 */
final class GenericRollResponseTest extends \Tests\TestCase
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
            'App\\Http\\Responses',
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
        new GenericRollResponse('5', GenericRollResponse::HTTP_OK, []);
    }

    /**
     * Test a simple roll with no addition or subtraction.
     * @test
     */
    public function testSimple(): void
    {
        $this->channel = Channel::factory()->create();
        $this->randomInt->expects(self::exactly(3))->willReturn(2);
        $response = new GenericRollResponse(
            '3d6',
            GenericRollResponse::HTTP_OK,
            [],
            $this->channel
        );
        $response = json_decode((string)$response);
        self::assertSame('Rolls: 2, 2, 2', $response->attachments[0]->footer);
        self::assertSame(
            'Rolling: 3d6 = [6] = 6',
            $response->attachments[0]->text
        );
    }

    /**
     * Test a simple roll with a description
     * @test
     */
    public function testWithDescription(): void
    {
        $this->channel = Channel::factory()->create();
        $this->randomInt->expects(self::exactly(4))->willReturn(3);
        $response = new GenericRollResponse(
            '4d6 testing',
            GenericRollResponse::HTTP_OK,
            [],
            $this->channel
        );
        $response = json_decode((string)$response);
        self::assertSame(
            'Rolls: 3, 3, 3, 3',
            $response->attachments[0]->footer
        );
        self::assertSame(
            'Rolling: 4d6 = [12] = 12',
            $response->attachments[0]->text
        );
        self::assertSame(
            'Unknown rolled 12 for "testing"',
            $response->attachments[0]->title
        );
    }

    /**
     * Test a more complex calculation.
     * @test
     */
    public function testWithCalculation(): void
    {
        $this->channel = Channel::factory()->create();
        $this->randomInt->expects(self::exactly(1))->willReturn(10);
        $response = new GenericRollResponse(
            '4+1d10-1*10 foo',
            GenericRollResponse::HTTP_OK,
            [],
            $this->channel
        );
        $response = json_decode((string)$response);
        self::assertSame('Rolls: 10', $response->attachments[0]->footer);
        self::assertSame(
            'Rolling: 4+1d10-1*10 = 4+[10]-1*10 = 4',
            $response->attachments[0]->text
        );
    }
}
