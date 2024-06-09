<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Shadowrun5e;

use App\Exceptions\SlackException;
use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\Shadowrun5e\Character;
use App\Rolls\Shadowrun5e\Luck;
use Facades\App\Services\DiceService;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function json_decode;
use function sprintf;

use const PHP_EOL;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Medium]
final class LuckTest extends TestCase
{
    /**
     * Test trying to roll a luck test without a character linked in Slack.
     */
    #[Group('slack')]
    public function testWithoutCharacterSlack(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'You must have a character linked to make luck tests'
        );
        (new Luck('', 'username', $channel))->forSlack();
    }

    /**
     * Test trying to roll a luck test without a character linked in Discord.
     */
    #[Group('discord')]
    public function testWithoutCharacterDiscord(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);

        self::assertSame(
            'username, You must have a character linked to make luck tests',
            (new Luck('', 'username', $channel))->forDiscord()
        );
    }

    #[Group('irc')]
    public function testWithoutCharacterIrc(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);

        self::assertSame(
            'username, You must have a character linked to make luck tests',
            (new Luck('', 'username', $channel))->forIrc()
        );
    }

    /**
     * Test a character doing with would be a critical glitch on a luck test.
     */
    #[Group('slack')]
    public function testCritGlitch(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(3)
            ->with(6)
            ->andReturn(1);

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
            'edge' => 3,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Luck('', 'username', $channel))->forSlack();
        $response = json_decode((string)$response)->attachments[0];
        self::assertSame(
            sprintf('%s rolled 3 dice for a luck test', $character),
            $response->title
        );
        self::assertSame('Rolled 0 successes', $response->text);
        $character->delete();
    }

    /**
     * Test a non-glitch luck test.
     */
    #[Group('discord')]
    public function testLuck(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(7)
            ->with(6)
            ->andReturn(6);

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
            'edge' => 7,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Luck('', 'username', $channel))->forDiscord();
        self::assertSame(
            sprintf(
                '**%s rolled 7 dice for a luck test**'
                    . PHP_EOL . 'Rolled 7 successes' . PHP_EOL
                    . 'Rolls: 6 6 6 6 6 6 6, Probability: 0.0457%%',
                (string)$character
            ),
            $response
        );
        $character->delete();
    }

    #[Group('irc')]
    public function testLuckIrc(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(7)
            ->with(6)
            ->andReturn(6);

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
            'edge' => 7,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Luck('', 'username', $channel))->forIrc();
        self::assertSame(
            sprintf(
                '%s rolled 7 dice for a luck test'
                    . PHP_EOL . 'Rolled 7 successes' . PHP_EOL
                    . 'Rolls: 6 6 6 6 6 6 6',
                (string)$character
            ),
            $response
        );
        $character->delete();
    }
}
