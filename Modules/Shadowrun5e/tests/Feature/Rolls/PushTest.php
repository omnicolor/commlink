<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Rolls;

use App\Enums\ChannelType;
use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use Facades\App\Services\DiceService;
use Modules\Shadowrun5e\Models\Character;
use Modules\Shadowrun5e\Rolls\Push;
use Omnicolor\Slack\Attachment;
use Omnicolor\Slack\Exceptions\SlackException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use const PHP_EOL;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Medium]
final class PushTest extends TestCase
{
    /**
     * Test trying to push the limit without having a linked character.
     */
    #[Group('slack')]
    public function testPushNoCharacter(): void
    {
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);
        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'You must have a character linked to push the limit',
        );
        (new Push('push 5', 'user', $channel))->forSlack();
    }

    /**
     * Test trying to push the limit without saying how many dice to roll.
     */
    #[Group('slack')]
    public function testPushNoDice(): void
    {
        $channel = Channel::factory()->create([
            'type' => ChannelType::Slack,
            'system' => 'shadowrun5e',
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ]);

        $character = Character::factory()->create([]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'Pushing the limit requires the number of dice to roll (not '
            . 'including your edge)',
        );
        (new Push('push foo', 'username', $channel))->forSlack();

        $character->delete();
    }

    /**
     * Test trying to push the limit in Slack with more than 100 dice.
     */
    #[Group('discord')]
    public function testPushTooManyDice(): void
    {
        $channel = Channel::factory()->create([
            'type' => ChannelType::Discord,
            'system' => 'shadowrun5e',
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'verified' => true,
        ]);

        $character = Character::factory()->create(['edge' => 2]);

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
     */
    #[Group('slack')]
    public function testPushOutOfEdge(): void
    {
        $channel = Channel::factory()->create([
            'type' => ChannelType::Slack,
            'system' => 'shadowrun5e',
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ]);

        $character = Character::factory()->create(['edgeCurrent' => 0]);

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
     */
    #[Group('discord')]
    public function testPushPastLimit(): void
    {
        DiceService::shouldReceive('rollOne')->times(8)->with(6)->andReturn(5);

        $channel = Channel::factory()->create([
            'type' => ChannelType::Discord,
            'system' => 'shadowrun5e',
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'verified' => true,
        ]);

        $character = Character::factory()->create([
            'edge' => 4,
            'edgeCurrent' => 3,
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
     */
    #[Group('slack')]
    public function testExplodingSixes(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(4)
            ->with(6)
            ->andReturn(6, 5, 6, 1);

        $channel = Channel::factory()->create([
            'type' => ChannelType::Slack,
            'system' => 'shadowrun5e',
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ]);

        $character = Character::factory()->create(['edge' => 1]);
        self::assertNull($character->edgeCurrent);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Push('push 1 6 testing', 'username', $channel))
            ->forSlack()
            ->jsonSerialize();

        self::assertSame('in_channel', $response['response_type']);
        self::assertArrayHasKey('attachments', $response);
        self::assertSame(
            [
                'color' => Attachment::COLOR_SUCCESS,
                'footer' => '*6* *6* *5* ~1~ (2 exploded), limit: 6',
                'text' => 'Rolled 3 successes',
                'title' => $character->handle . ' rolled 2 (1 requested + 1 '
                    . 'edge) dice for "testing" with a limit of 6',
            ],
            $response['attachments'][0],
        );

        // Verify character used some edge.
        $character->refresh();
        self::assertSame(0, $character->edgeCurrent);

        $character->delete();
    }

    /**
     * Test a character managing to still critical glitch when pushing the
     * limit.
     */
    #[Group('slack')]
    public function testPushCriticalGlitch(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(3)
            ->with(6)
            ->andReturn(2, 1, 1);

        $channel = Channel::factory()->create([
            'type' => ChannelType::Slack,
            'system' => 'shadowrun5e',
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ]);

        $character = Character::factory()->create(['edge' => 1]);
        self::assertNull($character->edgeCurrent);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Push('push 2', 'username', $channel))
            ->forSlack()
            ->jsonSerialize();
        self::assertSame('in_channel', $response['response_type']);
        self::assertArrayHasKey('attachments', $response);
        self::assertSame(
            [
                'color' => Attachment::COLOR_DANGER,
                'footer' => '2 ~1~ ~1~ (0 exploded)',
                'text' => 'Rolled 2 ones with no successes!',
                'title' => $character->handle . ' rolled a critical glitch on '
                    . '3 (2 requested + 1 edge) dice!',
            ],
            $response['attachments'][0],
        );

        // Make sure it still used some edge.
        $character->refresh();
        self::assertSame(0, $character->edgeCurrent);

        $character->delete();
    }

    #[Group('irc')]
    public function testPushPastLimitIrc(): void
    {
        DiceService::shouldReceive('rollOne')->times(8)->with(6)->andReturn(5);

        $channel = Channel::factory()->create([
            'type' => ChannelType::Irc,
            'system' => 'shadowrun5e',
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_IRC,
            'verified' => true,
        ]);

        $character = Character::factory()->create([
            'edge' => 4,
            'edgeCurrent' => 3,
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

    #[Group('irc')]
    public function testPushErrorIrc(): void
    {
        $channel = Channel::factory()->create([
            'type' => ChannelType::Discord,
            'system' => 'shadowrun5e',
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'verified' => true,
        ]);

        $character = Character::factory()->create(['edge' => 2]);

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
