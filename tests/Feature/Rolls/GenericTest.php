<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls;

use App\Models\Channel;
use App\Rolls\Generic;

/**
 * Tests for rolling generic dice.
 * @group discord
 * @group slack
 * @medium
 */
final class GenericTest extends \Tests\TestCase
{
    use \phpmock\phpunit\PHPMock;

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
        $this->randomInt = $this->getFunctionMock('App\\Rolls', 'random_int');
    }

    /**
     * Test a simple roll with no addition or subtraction.
     * @test
     */
    public function testSimple(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make();
        $this->randomInt->expects(self::exactly(3))->willReturn(2);
        $response = new Generic('3d6', 'username', $channel);
        $response = \json_decode((string)$response->forSlack());
        self::assertSame('Rolls: 2, 2, 2', $response->attachments[0]->footer);
        self::assertSame(
            'Rolling: 3d6 = [6] = 6',
            $response->attachments[0]->text
        );
    }

    /**
     * Test a simple roll with a description.
     * @test
     */
    public function testWithDescription(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make();
        $this->randomInt->expects(self::exactly(4))->willReturn(3);
        $roll = new Generic('4d6 testing', 'user', $channel);
        $response = \json_decode((string)$roll->forSlack());
        self::assertSame(
            'Rolls: 3, 3, 3, 3',
            $response->attachments[0]->footer
        );
        self::assertSame(
            'Rolling: 4d6 = [12] = 12',
            $response->attachments[0]->text
        );
        self::assertSame(
            'user rolled 12 for "testing"',
            $response->attachments[0]->title
        );

        $expected = '**user rolled 12 for "testing"**' . \PHP_EOL
            . 'Rolling: 4d6 = [12] = 12' . \PHP_EOL
            . '_Rolls: 3, 3, 3, 3_';
        $discord = $roll->forDiscord();
        self::assertSame($expected, $discord);
    }

    /**
     * Test a more complex calculation.
     * @test
     */
    public function testWithCalculation(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make();
        $this->randomInt->expects(self::exactly(2))->willReturn(10);
        $roll = new Generic('4+2d10-1*10 foo', 'Bob', $channel);
        $response = \json_decode((string)$roll->forSlack());
        self::assertSame('Rolls: 10, 10', $response->attachments[0]->footer);
        self::assertSame(
            'Rolling: 4+2d10-1*10 = 4+[20]-1*10 = 14',
            $response->attachments[0]->text
        );

        $expected = '**Bob rolled 14 for "foo"**' . \PHP_EOL
            . 'Rolling: 4+2d10-1*10 = 4+[20]-1*10 = 14' . \PHP_EOL
            . '_Rolls: 10, 10_';
        $discord = $roll->forDiscord();
        self::assertSame($expected, $discord);
    }
}
