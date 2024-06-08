<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Cyberpunkred;

use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\Cyberpunkred\Character;
use App\Rolls\Cyberpunkred\Help;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

/**
 * Tests for getting help in Cyberpunk Red.
 * @group cyberpunkred
 */
#[Medium]
final class HelpTest extends TestCase
{
    /**
     * Test getting help in a Cyberpunk Red Slack channel with no user or
     * campaign registered.
     * @group slack
     */
    public function testGetSlackHelpNothingRegistered(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $channel->username = 'user';

        $response = (new Help('help', 'user', $channel))->forSlack()->original;
        self::assertSame('ephemeral', $response['response_type']);
        self::assertSame(
            sprintf('About %s', config('app.name')),
            $response['attachments'][0]['title']
        );
        self::assertSame(
            'Note for unregistered users:',
            $response['attachments'][1]['title']
        );
        self::assertSame(
            'Cyberpunk Red commands (no character linked):',
            $response['attachments'][2]['title']
        );
    }

    /**
     * Test getting help in a Cyberpunk Red Slack channel with a user
     * registered, but no campaign or character.
     * @group slack
     */
    public function testGetSlackHelpWithChatUser(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make([
            'system' => 'cyberpunkred',
            'type' => Channel::TYPE_SLACK,
        ]);
        $channel->username = 'user';
        $channel->user = 'U' . Str::random(10);

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ]);

        $response = (new Help('help', 'user', $channel))->forSlack()->original;
        self::assertSame('ephemeral', $response['response_type']);
        self::assertSame(
            sprintf('About %s', config('app.name')),
            $response['attachments'][0]['title']
        );
        self::assertSame(
            'Cyberpunk Red commands (no character linked):',
            $response['attachments'][1]['title']
        );
    }

    /**
     * Test getting help in a Cyberpunk Red Slack channel with a character
     * linked to the channel.
     * @group slack
     */
    public function testGetSlackHelpWithCharacter(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->create([
            'system' => 'cyberpunkred',
            'type' => Channel::TYPE_SLACK,
        ]);
        $channel->username = 'user';
        $channel->user = 'U' . Str::random(10);

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
            'channel_id' => $channel,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser,
        ]);

        $response = (new Help('help', 'user', $channel))->forSlack()->original;
        self::assertSame('ephemeral', $response['response_type']);
        self::assertSame(
            sprintf('About %s', config('app.name')),
            $response['attachments'][0]['title']
        );
        self::assertSame(
            sprintf('Cyberpunk Red commands (as %s):', (string)$character),
            $response['attachments'][1]['title']
        );
        $character->delete();
    }

    /**
     * Test getting help in a Cyberpunk Red Discord channel with a character
     * linked to the channel.
     * @group discord
     */
    public function testGetDiscordHelpWithCharacter(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->create([
            'system' => 'cyberpunkred',
            'type' => Channel::TYPE_DISCORD,
        ]);
        $channel->username = 'user';
        $channel->user = 'U' . Str::random(10);

        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'verified' => true,
        ]);

        /** @var Character */
        $character = Character::factory()->create([
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser,
        ]);

        $response = (new Help('help', 'user', $channel))->forDiscord();
        self::assertStringContainsString(
            sprintf('About %s', config('app.name')),
            $response
        );
        self::assertStringContainsString(
            sprintf('Cyberpunk Red commands (as %s):', (string)$character),
            $response
        );
        $character->delete();
    }

    /**
     * Test getting help in a Cyberpunk Red IRC channel with no character
     * linked.
     * @group irc
     */
    public function testGetHelpIrc(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->create([
            'system' => 'cyberpunkred',
            'type' => Channel::TYPE_IRC,
        ]);
        $channel->username = 'user';
        $channel->user = 'user';

        $response = (new Help('help', 'user', $channel))->forIrc();
        self::assertStringContainsString(
            'Your IRC user has not been linked',
            $response
        );
    }
}
