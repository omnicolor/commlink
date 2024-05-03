<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Shadowrun5e;

use App\Exceptions\SlackException;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\Shadowrun5e\Character;
use App\Rolls\Shadowrun5e\Push;
use Facades\App\Services\DiceService;
use Tests\TestCase;

use const PHP_EOL;

/**
 * Tests for pushing the limit in Shadowrun 5E.
 * @group shadowrun
 * @group shadowrun5e
 * @medium
 */
final class PushTest extends TestCase
{
    /**
     * Test trying to push the limit without having a linked character.
     * @group slack
     * @test
     */
    public function testPushNoCharacter(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);
        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'You must have a character linked to push the limit'
        );
        (new Push('push 5', 'user', $channel))->forSlack();
    }

    /**
     * Test trying to push the limit without saying how many dice to roll.
     * @group Slack
     * @test
     */
    public function testPushNoDice(): void
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
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'Pushing the limit requires the number of dice to roll (not '
            . 'including your edge)'
        );
        (new Push('push foo', 'username', $channel))->forSlack();

        $character->delete();
    }

    /**
     * Test trying to push the limit in Slack with more than 100 dice.
     * @group discord
     * @test
     */
    public function testPushTooManyDice(): void
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
            'edge' => 2,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Push('push 101', 'username', $channel))->forDiscord();
        self::assertSame('You can\'t roll more than 100 dice', $response);

        $character->delete();
    }

    /**
     * Test trying to push the limit on a character with no edge.
     * @group slack
     * @test
     */
    public function testPushOutOfEdge(): void
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
            'edgeCurrent' => 0,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage('It looks like you\'re out of edge!');
        (new Push('push 10', 'username', $channel))->forSlack();

        $character->delete();
    }

    /**
     * Test pushing the limit and blowing past the limit.
     * @group discord
     * @test
     */
    public function testPushPastLimit(): void
    {
        DiceService::shouldReceive('rollOne')->times(8)->with(6)->andReturn(5);

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
            'edge' => 4,
            'edgeCurrent' => 3,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Push('push 4 5', 'username', $channel))->forDiscord();
        $expected = '**' . $character->handle . ' rolled 8 (4 requested + 4 '
            . 'edge) dice with a limit of 5**' . PHP_EOL
            . 'Rolled 8 successes, blew past limit' . PHP_EOL
            . 'Rolls: 5 5 5 5 5 5 5 5 (0 exploded), Limit: 5';
        self::assertSame($expected, $response);

        $character->refresh();
        self::assertSame(2, $character->edgeCurrent);

        $character->delete();
    }

    /**
     * Test pushing the limit with some exploding sixes.
     * @group slack
     * @test
     */
    public function testExplodingSixes(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(4)
            ->with(6)
            ->andReturn(6, 5, 6, 1);

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
            'edge' => 1,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        self::assertNull($character->edgeCurrent);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Push('push 1 6 testing', 'username', $channel))
            ->forSlack();
        $response = json_decode((string)$response);
        self::assertSame('in_channel', $response->response_type);
        $attachment = $response->attachments[0];
        self::assertSame(SlackResponse::COLOR_SUCCESS, $attachment->color);
        self::assertSame(
            '*6* *6* *5* ~1~ (2 exploded), limit: 6',
            $attachment->footer,
        );
        self::assertSame(
            $character->handle . ' rolled 2 (1 requested + 1 edge) dice for '
            . '"testing" with a limit of 6',
            $attachment->title
        );
        self::assertSame('Rolled 3 successes', $attachment->text);

        // Verify character used some edge.
        $character->refresh();
        self::assertSame(0, $character->edgeCurrent);

        $character->delete();
    }

    /**
     * Test a character managing to still critical glitch when pushing the
     * limit.
     * @group slack
     * @test
     */
    public function testPushCriticalGlitch(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(3)
            ->with(6)
            ->andReturn(2, 1, 1);

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
            'edge' => 1,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        self::assertNull($character->edgeCurrent);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Push('push 2', 'username', $channel))->forSlack();
        $response = json_decode((string)$response);
        self::assertSame('in_channel', $response->response_type);
        $attachment = $response->attachments[0];
        self::assertSame(SlackResponse::COLOR_DANGER, $attachment->color);
        self::assertSame('2 ~1~ ~1~ (0 exploded)', $attachment->footer);
        self::assertSame(
            $character->handle . ' rolled a critical glitch on 3 (2 requested '
            . '+ 1 edge) dice!',
            $attachment->title
        );
        self::assertSame('Rolled 2 ones with no successes!', $attachment->text);

        // Make sure it still used some edge.
        $character->refresh();
        self::assertSame(0, $character->edgeCurrent);

        $character->delete();
    }

    /**
     * @group irc
     */
    public function testPushPastLimitIrc(): void
    {
        DiceService::shouldReceive('rollOne')->times(8)->with(6)->andReturn(5);

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
            'edge' => 4,
            'edgeCurrent' => 3,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Push('push 4 5', 'username', $channel))->forIrc();
        $expected = $character->handle . ' rolled 8 (4 requested + 4 '
            . 'edge) dice with a limit of 5' . PHP_EOL
            . 'Rolled 8 successes, blew past limit' . PHP_EOL
            . 'Rolls: 5 5 5 5 5 5 5 5 (0 exploded), Limit: 5';
        self::assertSame($expected, $response);

        $character->delete();
    }

    /**
     * @group irc
     */
    public function testPushErrorIrc(): void
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
            'edge' => 2,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Push('push 101', 'username', $channel))->forIrc();
        self::assertSame('You can\'t roll more than 100 dice', $response);

        $character->delete();
    }
}
