<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Expanse;

use App\Models\Channel;
use App\Rolls\Expanse\Number;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

/**
 * Tests for rolling dice in The Expanse.
 * @group expanse
 * @medium
 */
final class NumberTest extends TestCase
{
    use PHPMock;

    protected MockObject $randomInt;

    public function setUp(): void
    {
        parent::setUp();
        $this->randomInt = $this->getFunctionMock(
            'App\\Rolls\\Expanse',
            'random_int'
        );
    }

    /**
     * Test a basic roll generating stunt points in Slack without a description.
     * @group slack
     * @test
     */
    public function testSimpleRollSlack(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'expanse']);

        $this->randomInt->expects(self::any())->willReturn(3);
        $response = \json_decode(
            (string)(new Number('5', 'user', $channel))->forSlack()
        );
        self::assertCount(1, $response->attachments);
        self::assertSame('user made a roll', $response->attachments[0]->title);
        self::assertSame('14 (3 SP)', $response->attachments[0]->text);
        self::assertSame('3 3 `3`', $response->attachments[0]->footer);
    }

    /**
     * Test a basic roll generating stunt points in Discord without a
     * description.
     * @group discord
     * @test
     */
    public function testSimpleRollDiscord(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'expanse']);

        $this->randomInt->expects(self::any())->willReturn(3);
        $response = (new Number('5', 'user', $channel))->forDiscord();
        $response = explode(\PHP_EOL, $response);
        self::assertSame('**user made a roll**', $response[0]);
        self::assertSame('14 (3 SP)', $response[1]);
    }

    /**
     * Test a basic roll not generating stunt points in Discord with a
     * description.
     * @group discord
     * @test
     */
    public function testRollWithDescriptionDiscord(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'expanse']);

        $this->randomInt->expects(self::any())
            ->willReturn(self::onConsecutiveCalls(2, 3, 5));
        $response = (new Number('5 percept', 'user', $channel))->forDiscord();
        $response = explode(\PHP_EOL, $response);
        self::assertSame('**user made a roll for "percept"**', $response[0]);
        self::assertSame('15', $response[1]);
    }

    /**
     * Test a basic roll not generating stunt points in IRC with a description.
     * @group irc
     * @test
     */
    public function testRollWithDescriptionIrc(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'expanse']);

        $this->randomInt->expects(self::any())
            ->willReturn(self::onConsecutiveCalls(2, 3, 5));
        $response = (new Number('5 percept', 'user', $channel))->forIrc();
        $response = explode(\PHP_EOL, $response);
        self::assertSame('user made a roll for "percept"', $response[0]);
        self::assertSame('15', $response[1]);
    }
}
