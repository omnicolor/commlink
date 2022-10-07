<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Shadowrun5e;

use App\Exceptions\SlackException;
use App\Models\Channel;
use App\Rolls\Shadowrun5e\Number;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

/**
 * Tests for rolling dice in Shadowrun 5E.
 * @group discord
 * @group shadowrun
 * @group shadowrun5e
 * @group slack
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
            'App\\Rolls\\Shadowrun5e',
            'random_int'
        );
    }

    /**
     * Test trying to roll without a limit or description.
     * @test
     */
    public function testRollNoLimitNoDescription(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);
        $this->randomInt->expects(self::any())->willReturn(random_int(1, 6));
        $response = new Number('5', 'user', $channel);
        $response = (string)$response->forSlack();
        self::assertStringNotContainsString('limit', $response);
        self::assertStringNotContainsString('for', $response);
    }

    /**
     * Test trying to roll with a limit.
     * @test
     */
    public function testRollWithLimit(): void
    {
        $this->randomInt->expects(self::any())->willReturn(random_int(1, 6));
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);
        $response = new Number('15 5', 'username', $channel);
        $response = (string)$response->forSlack();
        self::assertStringContainsString(', limit: 5', $response);
        self::assertStringNotContainsString('for', $response);
    }

    /**
     * Test trying to roll with a description.
     * @test
     */
    public function testRollWithDescription(): void
    {
        $this->randomInt->expects(self::any())->willReturn(random_int(1, 6));
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);
        $response = new Number('5 description', 'username', $channel);
        $response = (string)$response->forSlack();
        self::assertStringNotContainsString('limit', $response);
        self::assertStringContainsString('for \\"description\\"', $response);
    }

    /**
     * Test trying to roll with both a description and a limit.
     * @test
     */
    public function testRollBoth(): void
    {
        $this->randomInt->expects(self::any())->willReturn(random_int(1, 6));
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);
        $response = new Number('20 10 description', 'username', $channel);
        $response = (string)$response->forSlack();
        self::assertStringContainsString('limit: 10', $response);
        self::assertStringContainsString('for \\"description\\"', $response);
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
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);
        (new Number('101', 'username', $channel))->forSlack();
    }

    /**
     * Test the user rolling a critical glitch.
     * @test
     */
    public function testCriticalGlitch(): void
    {
        $this->randomInt->expects(self::exactly(3))->willReturn(1);
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);
        $response = new Number('3', 'username', $channel);
        $response = (string)$response->forSlack();
        self::assertStringContainsString(
            'username rolled a critical glitch on 3 dice!',
            $response
        );
    }

    /**
     * Test the footer formatting a user getting successes.
     * @test
     */
    public function testFooterSixes(): void
    {
        $this->randomInt->expects(self::exactly(3))->willReturn(6);
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);
        $response = new Number('3', 'username', $channel);
        $response = (string)$response->forSlack();
        self::assertStringContainsString('*6* *6* *6*', $response);
    }

    /**
     * Test the description when the roll hits the limit.
     * @test
     */
    public function testDescriptionHitLimit(): void
    {
        $this->randomInt->expects(self::exactly(6))->willReturn(5);
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);
        $response = new Number('6 3 shooting', 'username', $channel);
        $response = (string)$response->forSlack();
        self::assertStringContainsString(
            'Rolled 3 successes for \\"shooting\\", hit limit',
            $response
        );
    }

    /**
     * Test formatting a roll for Discord.
     * @test
     */
    public function testFormattedForDiscord(): void
    {
        $expected = '**username rolled 1 die**' . \PHP_EOL
            . 'Rolled 1 successes' . \PHP_EOL
            . 'Rolls: 6';
        $this->randomInt->expects(self::exactly(1))->willReturn(6);
        $response = new Number('1', 'username', new Channel());
        self::assertSame($expected, $response->forDiscord());
    }

    /**
     * Test formatting a roll for Discord with a limit and description.
     * @test
     */
    public function testFormattedForDiscordMaxedOut(): void
    {
        $expected = '**username rolled 6 dice with a limit of 3**' . \PHP_EOL
            . 'Rolled 3 successes, hit limit' . \PHP_EOL
            . 'Rolls: 6 6 6 6 6 6, Limit: 3';
        $this->randomInt->expects(self::exactly(6))->willReturn(6);
        $response = new Number('6 3', 'username', new Channel());
        self::assertSame($expected, $response->forDiscord());
    }

    /**
     * Test rolling too many dice in Discord.
     * @test
     */
    public function testFormattedForDiscordTooManyDice(): void
    {
        $this->randomInt->expects(self::never());
        $response = new Number('101', 'Loftwyr', new Channel());
        self::assertSame(
            'Loftwyr, you can\'t roll more than 100 dice!',
            $response->forDiscord()
        );
    }

    /**
     * Test rolling with too many initial spaces.
     * @test
     */
    public function testTooManySpaces(): void
    {
        $expected = '**username rolled 1 die**' . \PHP_EOL
            . 'Rolled 1 successes' . \PHP_EOL
            . 'Rolls: 6';
        $this->randomInt->expects(self::exactly(1))->willReturn(6);
        $response = new Number(' 1', 'username', new Channel());
        self::assertSame($expected, $response->forDiscord());
    }
}
