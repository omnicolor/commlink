<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Shadowrun5e;

use App\Exceptions\SlackException;
use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\Shadowrun5e\Character;
use App\Rolls\Shadowrun5e\Fade;
use Facades\App\Services\DiceService;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function sprintf;

use const PHP_EOL;

/**
 * Tests for rolling a fade test Shadowrun 5E.
 * @group shadowrun
 * @group shadowrun5e
 */
#[Medium]
final class FadeTest extends TestCase
{
    /**
     * Test trying to roll a fade test without a character linked in Slack.
     * @group slack
     */
    public function testWithoutCharacterSlack(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'You must have a character linked to make fade tests'
        );
        (new Fade('', 'username', $channel))->forSlack();
    }

    /**
     * Test trying to roll a fade test without a character linked in Discord.
     * @group discord
     */
    public function testWithoutCharacterDiscord(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);

        self::assertSame(
            'username, You must have a character linked to make fade tests',
            (new Fade('', 'username', $channel))->forDiscord()
        );
    }

    /**
     * Test trying to make a fade test without being a technomancer.
     * @group discord
     */
    public function testFadeNotTechnomancer(): void
    {
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
            'willpower' => 4,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Fade('', 'username', $channel))->forDiscord();
        self::assertSame(
            sprintf(
                '%s, Your character must have a resonance attribute to make '
                    . 'fading tests',
                $character,
            ),
            $response,
        );

        $character->delete();
    }

    /**
     * Test a fade test.
     * @group discord
     */
    public function testFadeDiscord(): void
    {
        DiceService::shouldReceive('rollOne')->times(11)->with(6)->andReturn(6);

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
            'resonance' => 6,
            'willpower' => 5,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Fade('', 'username', $channel))->forDiscord();
        self::assertSame(
            sprintf(
                '**%s rolled 11 dice for a fading test**'
                    . PHP_EOL . 'Rolled 11 successes' . PHP_EOL
                    . 'Rolls: 6 6 6 6 6 6 6 6 6 6 6, Probability: 0.0006%%',
                (string)$character
            ),
            $response
        );

        $character->delete();
    }

    /**
     * Test a fade test in Slack.
     * @group slack
     */
    public function testFadeSlack(): void
    {
        DiceService::shouldReceive('rollOne')->times(7)->with(6)->andReturn(2);

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
            'resonance' => 4,
            'willpower' => 3,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = json_decode(
            (string)(new Fade('', 'username', $channel))->forSlack()
        );
        $attachment = $response->attachments[0];
        self::assertSame(
            '2 2 2 2 2 2 2, Probability: 100.0000%',
            $attachment->footer
        );
        self::assertSame(
            sprintf('%s rolled 7 dice for a fading test', $character),
            $attachment->title
        );
        self::assertSame('Rolled 0 successes', $attachment->text);

        $character->delete();
    }

    /**
     * @group irc
     */
    public function testErrorIrc(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);

        self::assertSame(
            'username, You must have a character linked to make fade tests',
            (new Fade('', 'username', $channel))->forIrc()
        );
    }

    /**
     * Test a fade test.
     * @group irc
     */
    public function testFadeIRC(): void
    {
        DiceService::shouldReceive('rollOne')->times(11)->with(6)->andReturn(6);

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
            'resonance' => 6,
            'willpower' => 5,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Fade('', 'username', $channel))->forIrc();
        self::assertSame(
            sprintf(
                '%s rolled 11 dice for a fading test' . PHP_EOL
                    . 'Rolled 11 successes' . PHP_EOL
                    . 'Rolls: 6 6 6 6 6 6 6 6 6 6 6',
                (string)$character
            ),
            $response
        );

        $character->delete();
    }
}
