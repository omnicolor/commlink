<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\StarTrekAdventures;

use App\Models\Channel;
use App\Rolls\StarTrekAdventures\Unfocused;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

/**
 * Tests for trying an unfocused test in Star Trek Adventures.
 * @group star-trek-adventures
 * @small
 */
final class UnfocusedTest extends TestCase
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
     * Test making a simple unfocused roll.
     * @test
     */
    public function testUnfocusedSlack(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make();

        $this->randomInt->expects(self::exactly(2))->willReturn(3);

        $response = new Unfocused('unfocused 1 2 3', 'username', $channel);
        $response = \json_decode((string)$response->forSlack());
        $response = $response->attachments[0];

        self::assertSame('Rolls: 3 3', $response->footer);
        self::assertSame('Rolled 2 successes', $response->text);
        self::assertSame(
            'username failed a roll without a focus',
            $response->title
        );
    }

    /**
     * Test making an unfocused roll with extra dice.
     * @test
     */
    public function testUnfocusedExtraDice(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make();

        $this->randomInt->expects(self::exactly(6))->willReturn(3);

        $response = (new Unfocused('unfocused 1 2 3 4', 'username', $channel))
            ->forDiscord();

        $expected = '**username succeeded without a focus**' . \PHP_EOL
            . 'Rolled 6 successes' . \PHP_EOL . 'Rolls: 3 3 3 3 3 3';
        self::assertSame($expected, $response);
    }

    /**
     * Test making an unfocused roll resulting in a complication.
     * @test
     */
    public function testUnfocusedWithComplication(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make();

        $this->randomInt->expects(self::exactly(2))->willReturn(20);

        $response = (new Unfocused('unfocused 1 2 3', 'username', $channel))
            ->forDiscord();

        $expected = '**username failed a roll without a focus**' . \PHP_EOL
            . 'Rolled 0 successes with 2 complications' . \PHP_EOL
            . 'Rolls: 20 20';
        self::assertSame($expected, $response);
    }

    /**
     * Test getting extra successes with natural ones.
     * @test
     */
    public function testUnfocusedNaturalOnes(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make();

        $this->randomInt->expects(self::exactly(2))->willReturn(1);

        $response = (new Unfocused('unfocused 1 2 3', 'username', $channel))
            ->forDiscord();

        $expected = '**username succeeded without a focus**' . \PHP_EOL
            . 'Rolled 4 successes' . \PHP_EOL
            . 'Rolls: 1 1';
        self::assertSame($expected, $response);
    }

    /**
     * Test making an unfocused roll with optional text.
     * @test
     */
    public function testUnfocusedRollWithOptionalText(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make();

        $this->randomInt->expects(self::exactly(2))->willReturn(3);

        $response = new Unfocused(
            'unfocused 1 2 3 testing',
            'username',
            $channel
        );
        $response = \json_decode((string)$response->forSlack());
        $response = $response->attachments[0];

        self::assertSame('Rolls: 3 3', $response->footer);
        self::assertSame('Rolled 2 successes', $response->text);
        self::assertSame(
            'username failed a roll without a focus for "testing"',
            $response->title
        );
    }
}
