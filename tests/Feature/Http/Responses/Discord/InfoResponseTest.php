<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses\Discord;

use App\Events\DiscordMessageReceived;
use App\Http\Responses\Discord\InfoResponse;
use App\Models\Campaign;
use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\Shadowrun5e\Character;
use Discord\Discord;
use Discord\Parts\Channel\Channel as TextChannel;
use Discord\Parts\Channel\Message;
use Discord\Parts\Guild\Guild;
use Discord\Parts\User\User;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * @group discord
 * @medium
 */
final class InfoResponseTest extends TestCase
{
    /**
     * Create a mock Discord message.
     * @return Message
     */
    protected function createMessageMock(): Message
    {
        $serverNameAndId = Str::random(10);
        $serverStub = self::createStub(Guild::class);
        $serverStub->method('__get')->willReturn($serverNameAndId);

        $userTag = 'user#' . random_int(1000, 9999);
        $userId = (string)random_int(1, 9999);
        $userMap = [
            ['tag', $userTag],
            ['id', $userId],
        ];
        $userMock = $this->createMock(User::class);
        $userMock->method('__get')->willReturnMap($userMap);

        $channelName = Str::random(12);
        $channelId = Str::random(10);
        $channelMap = [
            ['guild', $serverStub],
            ['id', $channelId],
            ['name', $channelName],
        ];
        $channelMock = $this->createMock(TextChannel::class);
        $channelMock->method('__get')->willReturnMap($channelMap);

        $messageMap = [
            ['author', $userMock],
            ['channel', $channelMock],
            ['content', '/roll info'],
        ];
        $messageMock = self::createStub(Message::class);
        $messageMock->method('__get')->willReturnMap($messageMap);
        return $messageMock;
    }

    /**
     * Test trying to get info for an unregistered channel.
     * @test
     */
    public function testInfoUnregistered(): void
    {
        $messageMock = $this->createMessageMock();
        $event = new DiscordMessageReceived(
            $messageMock,
            self::createStub(Discord::class)
        );

        /** @var TextChannel */
        $channel = $event->channel;

        $expected = '**Debugging info**' . \PHP_EOL
            . 'User Tag: ' . optional($event->user)->displayname . \PHP_EOL
            . 'User ID: ' . optional($event->user)->id . \PHP_EOL
            . 'Server Name: ' . $event->server->name . \PHP_EOL
            . 'Server ID: ' . $event->server->id . \PHP_EOL
            // @phpstan-ignore-next-line
            . 'Channel Name: ' . $channel->name . \PHP_EOL
            . 'Channel ID: ' . $channel->id . \PHP_EOL
            . 'System: Unregistered' . \PHP_EOL
            . 'Character: No character' . \PHP_EOL
            . 'Campaign: No campaign';

        $response = new InfoResponse($event);
        self::assertSame($expected, (string)$response);
    }

    /**
     * Test trying to get info for a registered channel without a campaign.
     * @test
     */
    public function testInfoRegisteredNoCampaign(): void
    {
        $messageMock = $this->createMessageMock();

        /** @var TextChannel */
        $textChannel = $messageMock->channel;

        /** @var Channel */
        $channel = Channel::factory()->create([
            'channel_id' => $textChannel->id,
            // @phpstan-ignore-next-line
            'channel_name' => $textChannel->name,
            'server_id' => optional($textChannel->guild)->id,
            'server_name' => optional($textChannel->guild)->name,
            'system' => 'shadowrun5e',
            'type' => 'discord',
        ]);

        $event = new DiscordMessageReceived(
            $messageMock,
            self::createStub(Discord::class)
        );

        $expected = '**Debugging info**' . \PHP_EOL
            . 'User Tag: ' . optional($event->user)->displayname . \PHP_EOL
            . 'User ID: ' . optional($event->user)->id . \PHP_EOL
            . 'Server Name: ' . $channel->server_name . \PHP_EOL
            . 'Server ID: ' . $channel->server_id . \PHP_EOL
            . 'Channel Name: ' . $channel->channel_name . \PHP_EOL
            . 'Channel ID: ' . $channel->channel_id . \PHP_EOL
            . 'System: Shadowrun 5th Edition' . \PHP_EOL
            . 'Character: No character' . \PHP_EOL
            . 'Campaign: No campaign';

        $response = new InfoResponse($event);
        self::assertSame($expected, (string)$response);
    }

    /**
     * Test trying to get info for a channel.
     * @test
     */
    public function testInfoRegistered(): void
    {
        $messageMock = $this->createMessageMock();

        /** @var TextChannel */
        $textChannel = $messageMock->channel;

        /** @var Campaign */
        $campaign = Campaign::factory()->create([]);

        /** @var Channel */
        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
            'channel_id' => $textChannel->id,
            // @phpstan-ignore-next-line
            'channel_name' => $textChannel->name,
            'server_id' => optional($textChannel->guild)->id,
            'server_name' => optional($textChannel->guild)->name,
            'system' => 'shadowrun5e',
            'type' => 'discord',
        ]);

        $event = new DiscordMessageReceived(
            $messageMock,
            self::createStub(Discord::class)
        );

        $expected = '**Debugging info**' . \PHP_EOL
            . 'User Tag: ' . optional($event->user)->displayname . \PHP_EOL
            . 'User ID: ' . optional($event->user)->id . \PHP_EOL
            . 'Server Name: ' . $channel->server_name . \PHP_EOL
            . 'Server ID: ' . $channel->server_id . \PHP_EOL
            . 'Channel Name: ' . $channel->channel_name . \PHP_EOL
            . 'Channel ID: ' . $channel->channel_id . \PHP_EOL
            . 'System: Shadowrun 5th Edition' . \PHP_EOL
            . 'Character: No character' . \PHP_EOL
            . 'Campaign: ' . $campaign->name;

        $response = new InfoResponse($event);
        self::assertSame($expected, (string)$response);
    }

    /**
     * Test getting info if the user has registered a character to the channel.
     * @test
     */
    public function testInfoWithCharacter(): void
    {
        $messageMock = $this->createMessageMock();

        /** @var TextChannel */
        $textChannel = $messageMock->channel;

        /** @var Campaign */
        $campaign = Campaign::factory()->create([]);

        /** @var Channel */
        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
            'channel_id' => $textChannel->id,
            // @phpstan-ignore-next-line
            'channel_name' => $textChannel->name,
            'server_id' => optional($textChannel->guild)->id,
            'server_name' => optional($textChannel->guild)->name,
            'system' => 'shadowrun5e',
            'type' => Channel::TYPE_DISCORD,
        ]);

        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => optional($messageMock->author)->id,
            'server_id' => optional($textChannel->guild)->id,
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

        $event = new DiscordMessageReceived(
            $messageMock,
            self::createStub(Discord::class)
        );

        $expected = '**Debugging info**' . \PHP_EOL
            . 'User Tag: ' . optional($event->user)->displayname . \PHP_EOL
            . 'User ID: ' . optional($event->user)->id . \PHP_EOL
            . 'Server Name: ' . $channel->server_name . \PHP_EOL
            . 'Server ID: ' . $channel->server_id . \PHP_EOL
            . 'Channel Name: ' . $channel->channel_name . \PHP_EOL
            . 'Channel ID: ' . $channel->channel_id . \PHP_EOL
            . 'System: Shadowrun 5th Edition' . \PHP_EOL
            . 'Character: ' . (string)$character . \PHP_EOL
            . 'Campaign: ' . $campaign->name;

        $response = new InfoResponse($event);
        self::assertSame($expected, (string)$response);
    }
}
