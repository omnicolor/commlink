<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\StarTrekAdventures;

use App\Models\Channel;
use App\Rolls\StarTrekAdventures\Challenge;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

/**
 * Tests for rolling challenge dice in Star Trek Adventures.
 * @group star-trek-adventures
 * @medium
 */
final class ChallengeTest extends TestCase
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
     * Test a roll in Slack that produces no score.
     * @test
     */
    public function testNoScore(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['registered_by' => 1]);

        $this->randomInt->expects(self::exactly(3))->willReturn(3);

        $response = new Challenge('challenge 3', 'username', $channel);
        $response = \json_decode((string)$response->forSlack());
        $response = $response->attachments[0];

        self::assertSame('Rolls: 3 3 3', $response->footer);
        self::assertSame('Rolled 3 challenge dice', $response->text);
        self::assertSame(
            'username rolled a score of 0 without an Effect',
            $response->title
        );
    }

    /**
     * Test a roll in Discord that produces an effect.
     * @test
     */
    public function testWithEffect(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['registered_by' => 2]);

        $this->randomInt->expects(self::exactly(2))->willReturn(6);

        $response = (new Challenge('challenge 2', 'username', $channel))
            ->forDiscord();

        $expected = '**username rolled a score of 2 with an Effect**' . \PHP_EOL
            . 'Rolled 2 challenge dice' . \PHP_EOL
            . 'Rolls: 6 6';
        self::assertSame($expected, $response);
    }

    /**
     * Test a roll of one on two challenge dice with optional text.
     * @test
     */
    public function testWithText(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['registered_by' => 3]);

        $this->randomInt->expects(self::exactly(2))->willReturn(1);

        $response = (new Challenge('challenge 2 testing', 'username', $channel))
            ->forDiscord();

        $expected = '**username rolled a score of 2 without an Effect for '
            . '"testing"**' . \PHP_EOL . 'Rolled 2 challenge dice' . \PHP_EOL
            . 'Rolls: 1 1';
        self::assertSame($expected, $response);
    }
}
