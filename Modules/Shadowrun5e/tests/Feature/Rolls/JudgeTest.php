<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Rolls;

use App\Exceptions\SlackException;
use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use Facades\App\Services\DiceService;
use Modules\Shadowrun5e\Models\Character;
use Modules\Shadowrun5e\Rolls\Judge;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function json_decode;
use function sprintf;

use const PHP_EOL;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Medium]
final class JudgeTest extends TestCase
{
    /**
     * Test trying to roll a judge intentions test without a character linked in
     * Slack.
     */
    #[Group('slack')]
    public function testWithoutCharacterSlack(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'You must have a character linked to make judge intentions tests'
        );
        (new Judge('15 5', 'username', $channel))->forSlack();
    }

    /**
     * Test trying to roll a judge intentions test without a character linked in
     * Discord.
     */
    #[Group('discord')]
    public function testWithoutCharacterDiscord(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);

        self::assertSame(
            'username, You must have a character linked to make judge '
                . 'intentions tests',
            (new Judge('', 'username', $channel))->forDiscord()
        );
    }

    #[Group('irc')]
    public function testWithoutCharacterIrc(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);

        self::assertSame(
            'username, You must have a character linked to make judge '
                . 'intentions tests',
            (new Judge('', 'username', $channel))->forIrc()
        );
    }

    /**
     * Test a character critical glitching on a judge intentions test.
     */
    #[Group('slack')]
    public function testCritGlitch(): void
    {
        DiceService::shouldReceive('rollOne')->times(6)->with(6)->andReturn(1);

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
            'intuition' => 2,
            'created_by' => self::class . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Judge('', 'username', $channel))->forSlack();
        $response = json_decode((string)$response)->attachments[0];
        self::assertSame(
            sprintf(
                '%s critically glitched on a judge intentions roll!',
                $character
            ),
            $response->title
        );
        self::assertSame('Rolled 6 ones with no successes!', $response->text);

        $character->delete();
    }

    /**
     * Test a non-glitch judge intentions test.
     */
    #[Group('discord')]
    public function testJudge(): void
    {
        DiceService::shouldReceive('rollOne')->times(8)->with(6)->andReturn(6);

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
            'charisma' => 5,
            'intuition' => 3,
            'created_by' => self::class . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Judge('', 'username', $channel))->forDiscord();
        self::assertSame(
            sprintf(
                '**%s rolled 8 dice for a judge intentions test**'
                    . PHP_EOL . 'Rolled 8 successes' . PHP_EOL
                    . 'Rolls: 6 6 6 6 6 6 6 6, Probability: 0.0152%%',
                (string)$character
            ),
            $response
        );

        $character->delete();
    }

    #[Group('irc')]
    public function testJudgeIrc(): void
    {
        DiceService::shouldReceive('rollOne')->times(8)->with(6)->andReturn(6);

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
            'charisma' => 5,
            'intuition' => 3,
            'created_by' => self::class . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Judge('', 'username', $channel))->forIrc();
        self::assertSame(
            sprintf(
                '%s rolled 8 dice for a judge intentions test'
                    . PHP_EOL . 'Rolled 8 successes' . PHP_EOL
                    . 'Rolls: 6 6 6 6 6 6 6 6',
                (string)$character
            ),
            $response
        );

        $character->delete();
    }
}
