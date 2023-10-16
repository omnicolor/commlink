<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Avatar;

use App\Exceptions\SlackException;
use App\Models\Channel;
use App\Rolls\Avatar\Plead;
use Facades\App\Services\DiceService;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Tests for the Plead roll in Avatar.
 * @group avatar
 * @medium
 */
final class PleadTest extends TestCase
{
    use WithFaker;

    /**
     * Test trying to plead in a non-Avatar Slack channel.
     * @test
     */
    public function testWrongSystemSlack(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'capers']);
        $channel->username = $this->faker->name;

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'Avatar moves are only available for channels registered for the '
                . 'Avatar system.'
        );
        (new Plead('plead', $channel->username, $channel))->forSlack();
    }

    /**
     * Test trying to plead in a non-Avatar Discord channel.
     * @test
     */
    public function testWrongSystemDiscord(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'capers']);
        $channel->username = $this->faker->name;

        $response = (new Plead('plead', $channel->username, $channel))
            ->forDiscord();
        self::assertSame(
            'Avatar moves are only available for channels registered for the '
                . 'Avatar system.',
            $response
        );
    }

    /**
     * Test failing pleading with no additional arguments.
     * @test
     */
    public function testSimplePlead(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(2, 6)
            ->andReturn([4, 4]);

        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'avatar']);
        $channel->username = $this->faker->name;

        $response = (new Plead('plead', $channel->username, $channel))
            ->forDiscord();
        self::assertSame(
            \sprintf(
                "**%s is getting close to succeeding in pleading**\n2d6 = 4 + 4 = 8",
                $channel->username
            ),
            $response
        );
    }

    /**
     * Test pleading successfully with additional arguments.
     * @test
     */
    public function testPlead(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(2, 6)
            ->andReturn([6, 6]);

        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'avatar']);
        $channel->username = $this->faker->name;

        $response = (new Plead('plead 6 testing', $channel->username, $channel))
            ->forSlack();
        $response = \json_decode((string)$response);
        $response = $response->attachments[0];
        self::assertSame(
            \sprintf(
                '%s succeeded in a plead roll for "testing"',
                $channel->username
            ),
            $response->title
        );
        self::assertSame(
            '2d6 + 6 = 6 + 6 + 6 = 18',
            $response->text
        );
    }

    /**
     * Test failing pleading because of a negative modifier.
     * @test
     */
    public function testFailingPleadNegativeModifier(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(2, 6)
            ->andReturn([6, 6]);

        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'avatar']);
        $channel->username = $this->faker->name;

        $response = (new Plead('plead -8', $channel->username, $channel))
            ->forSlack();
        $response = \json_decode((string)$response);
        $response = $response->attachments[0];
        self::assertSame(
            \sprintf(
                '%s failed a plead roll',
                $channel->username
            ),
            $response->title
        );
        self::assertSame(
            '2d6 - 8 = 6 + 6 - 8 = 4',
            $response->text
        );
    }
}
