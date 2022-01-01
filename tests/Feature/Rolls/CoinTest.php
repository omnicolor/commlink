<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls;

use App\Models\Channel;
use App\Rolls\Coin;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests for flipping a coin.
 * @medium
 */
final class CoinTest extends \Tests\TestCase
{
    use \phpmock\phpunit\PHPMock;

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
        $this->randomInt = $this->getFunctionMock('App\\Rolls', 'random_int');
    }

    /**
     * Test a coin flip.
     * @group slack
     * @test
     */
    public function testTails(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make();
        $this->randomInt->expects(self::exactly(1))->willReturn(2);
        $response = new Coin('coin', 'username', $channel);
        $response = \json_decode((string)$response->forSlack());
        self::assertSame(
            'username flipped a coin: Tails',
            $response->attachments[0]->title
        );
    }

    /**
     * Test a coin flip.
     * @group discord
     * @test
     */
    public function testHeads(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make();
        $this->randomInt->expects(self::exactly(1))->willReturn(1);
        $response = new Coin('coin', 'username', $channel);
        $response = $response->forDiscord();
        self::assertSame(
            '**username flipped a coin: Heads**' . \PHP_EOL,
            $response
        );
    }
}
