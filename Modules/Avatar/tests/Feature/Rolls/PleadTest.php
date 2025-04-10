<?php

declare(strict_types=1);

namespace Modules\Avatar\Tests\Feature\Rolls;

use App\Models\Channel;
use Facades\App\Services\DiceService;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Avatar\Rolls\Plead;
use Omnicolor\Slack\Exceptions\SlackException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function sprintf;

#[Group('avatar')]
#[Medium]
final class PleadTest extends TestCase
{
    use WithFaker;

    /**
     * Test trying to plead in a non-Avatar Slack channel.
     */
    #[Group('slack')]
    public function testWrongSystemSlack(): void
    {
        $channel = Channel::factory()->make(['system' => 'capers']);
        $channel->username = $this->faker->name;

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'Avatar moves are only available for channels registered for the '
                . 'Avatar system.',
        );
        (new Plead('plead', $channel->username, $channel))->forSlack();
    }

    /**
     * Test trying to plead in a non-Avatar Discord channel.
     */
    #[Group('discord')]
    public function testWrongSystemDiscord(): void
    {
        $channel = Channel::factory()->make(['system' => 'capers']);
        $channel->username = $this->faker->name;

        $response = (new Plead('plead', $channel->username, $channel))
            ->forDiscord();
        self::assertSame(
            'Avatar moves are only available for channels registered for the '
                . 'Avatar system.',
            $response,
        );
    }

    /**
     * Test trying to plead in a non-Avatar IRC channel.
     */
    #[Group('irc')]
    public function testWrongSystemIrc(): void
    {
        $channel = Channel::factory()->make(['system' => 'capers']);
        $channel->username = $this->faker->name;

        $response = (new Plead('plead', $channel->username, $channel))
            ->forIrc();
        self::assertSame(
            'Avatar moves are only available for channels registered for the '
                . 'Avatar system.',
            $response,
        );
    }

    /**
     * Test failing pleading with no additional arguments.
     */
    #[Group('discord')]
    public function testSimplePleadDiscord(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(2, 6)
            ->andReturn([4, 4]);

        $channel = Channel::factory()->make(['system' => 'avatar']);
        $channel->username = $this->faker->name;

        $response = (new Plead('plead', $channel->username, $channel))
            ->forDiscord();
        self::assertSame(
            sprintf(
                "**%s is getting close to succeeding in pleading**\n2d6 = 4 + 4 = 8",
                $channel->username,
            ),
            $response,
        );
    }

    /**
     * Test failing pleading with no additional arguments.
     */
    #[Group('irc')]
    public function testSimplePleadIrc(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(2, 6)
            ->andReturn([4, 4]);

        $channel = Channel::factory()->make(['system' => 'avatar']);
        $channel->username = $this->faker->name;

        $response = (new Plead('plead', $channel->username, $channel))
            ->forIrc();
        self::assertSame(
            sprintf(
                "%s is getting close to succeeding in pleading\n2d6 = 4 + 4 = 8",
                $channel->username,
            ),
            $response,
        );
    }

    /**
     * Test pleading successfully with additional arguments.
     */
    #[Group('slack')]
    public function testPlead(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(2, 6)
            ->andReturn([6, 6]);

        $channel = Channel::factory()->make(['system' => 'avatar']);
        $channel->username = $this->faker->name;

        $response = (new Plead('plead 6 testing', $channel->username, $channel))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertArrayHasKey('text', $response['attachments'][0]);
        self::assertSame(
            sprintf(
                '%s succeeded in a plead roll for "testing"',
                $channel->username
            ),
            $response['attachments'][0]['title']
        );
        self::assertSame(
            '2d6 + 6 = 6 + 6 + 6 = 18',
            $response['attachments'][0]['text'],
        );
    }

    /**
     * Test failing pleading because of a negative modifier.
     */
    #[Group('slack')]
    public function testFailingPleadNegativeModifier(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(2, 6)
            ->andReturn([6, 6]);

        $channel = Channel::factory()->make(['system' => 'avatar']);
        $channel->username = $this->faker->name;

        $response = (new Plead('plead -8', $channel->username, $channel))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertArrayHasKey('text', $response['attachments'][0]);
        self::assertSame(
            sprintf(
                '%s failed a plead roll',
                $channel->username
            ),
            $response['attachments'][0]['title'],
        );
        self::assertSame(
            '2d6 - 8 = 6 + 6 - 8 = 4',
            $response['attachments'][0]['text'],
        );
    }
}
