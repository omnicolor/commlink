<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\StarTrekAdventures;

use App\Models\Channel;
use App\Rolls\StarTrekAdventures\Focused;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

/**
 * Tests for trying a focused test in Star Trek Adventures.
 * @group star-trek-adventures
 * @medium
 */
final class FocusedTest extends TestCase
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
            'App\\Rolls\\StarTrekAdventures',
            'random_int'
        );
    }

    /**
     * Test making a simple focused roll.
     * @test
     */
    public function testFocusedSlack(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make();

        $this->randomInt->expects(self::exactly(2))->willReturn(3);

        $response = new Focused('focused 1 2 3', 'username', $channel);
        $response = \json_decode((string)$response->forSlack());
        $response = $response->attachments[0];

        self::assertSame('Rolls: 3 3', $response->footer);
        self::assertSame('Rolled 2 successes', $response->text);
        self::assertSame(
            'username failed a roll with a focus',
            $response->title
        );
    }

    /**
     * Test making a focused roll with extra dice.
     * @test
     */
    public function testFocusedExtraDice(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make();

        $this->randomInt->expects(self::exactly(6))->willReturn(3);

        $response = (new Focused('focused 1 2 3 4', 'username', $channel))
            ->forDiscord();

        $expected = '**username succeeded with a focus**' . \PHP_EOL
            . 'Rolled 6 successes' . \PHP_EOL . 'Rolls: 3 3 3 3 3 3';
        self::assertSame($expected, $response);
    }

    /**
     * Test making an focused roll resulting in a complication.
     * @test
     */
    public function testFocusedWithComplication(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make();

        $this->randomInt->expects(self::exactly(2))->willReturn(20);

        $response = (new Focused('focused 1 2 3', 'username', $channel))
            ->forDiscord();

        $expected = '**username failed a roll with a focus**' . \PHP_EOL
            . 'Rolled 0 successes with 2 complications' . \PHP_EOL
            . 'Rolls: 20 20';
        self::assertSame($expected, $response);
    }

    /**
     * Test getting extra successes with natural ones.
     * @test
     */
    public function testFocusedNaturalOnes(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make();

        $this->randomInt->expects(self::exactly(2))->willReturn(1);

        $response = (new Focused('focused 1 2 3', 'username', $channel))
            ->forDiscord();

        $expected = '**username succeeded with a focus**' . \PHP_EOL
            . 'Rolled 4 successes' . \PHP_EOL
            . 'Rolls: 1 1';
        self::assertSame($expected, $response);
    }

    /**
     * Test making a focused roll with optional text.
     * @test
     */
    public function testFocusedRollWithOptionalText(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make();

        $this->randomInt->expects(self::exactly(2))->willReturn(3);

        $response = new Focused(
            'focused 1 2 3 testing',
            'username',
            $channel
        );
        $response = \json_decode((string)$response->forSlack());
        $response = $response->attachments[0];

        self::assertSame('Rolls: 3 3', $response->footer);
        self::assertSame('Rolled 2 successes', $response->text);
        self::assertSame(
            'username failed a roll with a focus for "testing"',
            $response->title
        );
    }
}
