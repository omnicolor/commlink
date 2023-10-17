<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses\Slack;

use App\Http\Responses\Slack\InfoResponse;
use App\Models\Campaign;
use App\Models\Channel;
use App\Models\Character;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

use function json_decode;

/**
 * Tests for getting info about the channel.
 * @group slack
 * @medium
 */
final class InfoResponseTest extends TestCase
{
    /**
     * Test getting info for an unregistered channel.
     * @test
     */
    public function testUnregistered(): void
    {
        $channel = new Channel([
            'channel_id' => 'channel id',
            'server_id' => 'server id',
        ]);
        $channel->user = 'user id';
        $response = new InfoResponse(channel: $channel);
        $response = json_decode((string)$response);
        self::assertSame('ephemeral', $response->response_type);
        self::assertEquals(
            [
                (object)[
                    'title' => 'Debugging Info',
                    'fields' => [
                        (object)[
                            'title' => 'Team ID',
                            'value' => 'server id',
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'Channel ID',
                            'value' => 'channel id',
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'User ID',
                            'value' => 'user id',
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'Commlink User',
                            'value' => 'Not linked',
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'System',
                            'value' => 'unregistered',
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'Character',
                            'value' => 'No character',
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'Campaign',
                            'value' => 'No campaign',
                            'short' => true,
                        ],
                    ],
                ],
            ],
            $response->attachments
        );
    }

    /**
     * Test getting info for a registered channel that doesn't have a character
     * registered.
     * @test
     */
    public function testRegisteredButNotLinked(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Channel */
        $channel = Channel::factory()->create(['type' => Channel::TYPE_SLACK]);
        $channel->user = Str::random(10);
        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => $channel->type,
            'user_id' => $user->id,
            'verified' => true,
        ]);

        $response = new InfoResponse(channel: $channel);
        $response = json_decode((string)$response);
        self::assertSame('ephemeral', $response->response_type);
        self::assertEquals(
            [
                (object)[
                    'title' => 'Debugging Info',
                    'fields' => [
                        (object)[
                            'title' => 'Team ID',
                            'value' => $channel->server_id,
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'Channel ID',
                            'value' => $channel->channel_id,
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'User ID',
                            'value' => $channel->user,
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'Commlink User',
                            'value' => $user->email,
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'System',
                            'value' => config('app.systems')[$channel->system],
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'Character',
                            'value' => 'No character',
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'Campaign',
                            'value' => 'No campaign',
                            'short' => true,
                        ],
                    ],
                ],
            ],
            $response->attachments
        );
    }

    /**
     * Test getting info for a registered channel with an invalid character ID.
     * @test
     */
    public function testRegisteredButCharacterNotFound(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Channel */
        $channel = Channel::factory()->create(['type' => Channel::TYPE_SLACK]);
        $channel->user = Str::random(10);
        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => $channel->type,
            'user_id' => $user->id,
            'verified' => true,
        ]);
        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => sha1(Str::random(10)),
            'chat_user_id' => $chatUser->id,
        ]);

        $response = new InfoResponse(channel: $channel);
        $response = json_decode((string)$response);
        self::assertSame('ephemeral', $response->response_type);
        self::assertEquals(
            [
                (object)[
                    'title' => 'Debugging Info',
                    'fields' => [
                        (object)[
                            'title' => 'Team ID',
                            'value' => $channel->server_id,
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'Channel ID',
                            'value' => $channel->channel_id,
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'User ID',
                            'value' => $channel->user,
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'Commlink User',
                            'value' => $user->email,
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'System',
                            'value' => config('app.systems')[$channel->system],
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'Character',
                            'value' => 'Invalid character',
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'Campaign',
                            'value' => 'No campaign',
                            'short' => true,
                        ],
                    ],
                ],
            ],
            $response->attachments
        );
    }

    /**
     * Test getting info for a registered channel.
     * @test
     */
    public function testRegisteredWithUser(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Channel */
        $channel = Channel::factory()->create(['type' => Channel::TYPE_SLACK]);
        $channel->user = Str::random(10);
        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => $channel->type,
            'user_id' => $user->id,
            'verified' => true,
        ]);
        /** @var Character */
        $character = Character::factory()->create([
            'owner' => $user->email,
            'system' => $channel->system,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = new InfoResponse(channel: $channel);
        $response = json_decode((string)$response);
        self::assertSame('ephemeral', $response->response_type);
        self::assertEquals(
            [
                (object)[
                    'title' => 'Debugging Info',
                    'fields' => [
                        (object)[
                            'title' => 'Team ID',
                            'value' => $channel->server_id,
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'Channel ID',
                            'value' => $channel->channel_id,
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'User ID',
                            'value' => $channel->user,
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'Commlink User',
                            'value' => $user->email,
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'System',
                            'value' => config('app.systems')[$channel->system],
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'Character',
                            'value' => (string)$character,
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'Campaign',
                            'value' => 'No campaign',
                            'short' => true,
                        ],
                    ],
                ],
            ],
            $response->attachments
        );
        $character->delete();
    }

    /**
     * Test getting info for a registered channel with a campaign.
     * @test
     */
    public function testRegisteredWithCampaign(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create([]);

        /** @var Channel */
        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
            'type' => Channel::TYPE_SLACK,
        ]);
        $channel->user = Str::random(10);

        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => $channel->type,
            'user_id' => $user->id,
            'verified' => true,
        ]);
        /** @var Character */
        $character = Character::factory()->create([
            'owner' => $user->email,
            'system' => $channel->system,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = new InfoResponse(channel: $channel);
        $response = json_decode((string)$response);
        self::assertSame('ephemeral', $response->response_type);
        self::assertEquals(
            [
                (object)[
                    'title' => 'Debugging Info',
                    'fields' => [
                        (object)[
                            'title' => 'Team ID',
                            'value' => $channel->server_id,
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'Channel ID',
                            'value' => $channel->channel_id,
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'User ID',
                            'value' => $channel->user,
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'Commlink User',
                            'value' => $user->email,
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'System',
                            'value' => config('app.systems')[$channel->system],
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'Character',
                            'value' => (string)$character,
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'Campaign',
                            'value' => $campaign->name,
                            'short' => true,
                        ],
                    ],
                ],
            ],
            $response->attachments
        );
        $character->delete();
    }
}
