<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Shadowrun5e;

use App\Exceptions\SlackException;
use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\Shadowrun5e\Character;
use App\Rolls\Shadowrun5e\Composure;
use Facades\App\Services\DiceService;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function json_decode;
use function sprintf;

use const PHP_EOL;

/**
 * Tests for rolling a composure test Shadowrun 5E.
 * @group shadowrun
 * @group shadowrun5e
 */
#[Medium]
final class ComposureTest extends TestCase
{
    /**
     * Test trying to roll a composure test without a character linked in Slack.
     * @group slack
     */
    public function testWithoutCharacterSlack(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'You must have a character linked to make Composure tests'
        );
        (new Composure('15 5', 'username', $channel))->forSlack();
    }

    /**
     * Test trying to roll a composure test without a character linked in
     * Discord.
     * @group discord
     */
    public function testWithoutCharacterDiscord(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);

        self::assertSame(
            'username, You must have a character linked to make Composure tests',
            (new Composure('', 'username', $channel))->forDiscord()
        );
    }

    /**
     * @group irc
     */
    public function testWithoutCharacterIrc(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);

        self::assertSame(
            'username, You must have a character linked to make Composure tests',
            (new Composure('', 'username', $channel))->forIrc()
        );
    }

    /**
     * Test a character critical glitching on a Composure test.
     * @group slack
     */
    public function testCritGlitch(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_SLACK,
            'system' => 'shadowrun5e',
        ]);

        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ]);

        /** @var Character */
        $character = Character::factory()->create([
            'charisma' => 4,
            'willpower' => 2,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        DiceService::shouldReceive('rollOne')
            ->times(6)
            ->with(6)
            ->andReturn(1);
        $response = (new Composure('', 'username', $channel))->forSlack();
        $response = json_decode((string)$response)->attachments[0];
        self::assertSame(
            sprintf(
                '%s critically glitched on a composure roll!',
                $character
            ),
            $response->title
        );
        self::assertSame('Rolled 6 ones with no successes!', $response->text);

        $character->delete();
    }

    /**
     * Test a non-glitch composure test.
     * @group discord
     */
    public function testComposure(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_DISCORD,
            'system' => 'shadowrun5e',
        ]);

        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'verified' => true,
        ]);

        /** @var Character */
        $character = Character::factory()->create([
            'charisma' => 5,
            'willpower' => 3,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        DiceService::shouldReceive('rollOne')
            ->times(8)
            ->with(6)
            ->andReturn(6);
        $response = (new Composure('', 'username', $channel))->forDiscord();
        self::assertSame(
            sprintf(
                '**%s rolled 8 dice for a composure test**'
                    . PHP_EOL . 'Rolled 8 successes' . PHP_EOL
                    . 'Rolls: 6 6 6 6 6 6 6 6, Probability: 0.0152%%',
                (string)$character
            ),
            $response
        );

        $character->delete();
    }

    /**
     * Test a non-glitch composure test.
     * @group irc
     */
    public function testComposureIRC(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(8)
            ->with(6)
            ->andReturn(6);

        /** @var Channel */
        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_IRC,
            'system' => 'shadowrun5e',
        ]);

        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_IRC,
            'verified' => true,
        ]);

        /** @var Character */
        $character = Character::factory()->create([
            'charisma' => 5,
            'willpower' => 3,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Composure('', 'username', $channel))->forIrc();
        self::assertSame(
            sprintf(
                '%s rolled 8 dice for a composure test'
                    . PHP_EOL . 'Rolled 8 successes' . PHP_EOL
                    . 'Rolls: 6 6 6 6 6 6 6 6',
                (string)$character
            ),
            $response
        );

        $character->delete();
    }
}
