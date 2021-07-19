<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses;

use App\Http\Responses\InfoResponse;
use App\Models\Channel;
use App\Models\Character;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\User;

/**
 * Tests for getting info about the channel.
 * @covers \App\Http\Responses\InfoResponse
 * @group slack
 * @medium
 */
final class InfoResponseTest extends \Tests\TestCase
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
        $response = new InfoResponse('', InfoResponse::HTTP_OK, [], $channel);
        $response = \json_decode((string)$response);
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
        $channel = Channel::factory()->create();
        $channel->user = \Str::random(10);
        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => $channel->type,
            'user_id' => $user->id,
            'verified' => true,
        ]);

        $response = new InfoResponse('', InfoResponse::HTTP_OK, [], $channel);
        $response = \json_decode((string)$response);
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
        $channel = Channel::factory()->create();
        $channel->user = \Str::random(10);
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
            'character_id' => sha1(\Str::random(10)),
            'chat_user_id' => $chatUser->id,
        ]);

        $response = new InfoResponse('', InfoResponse::HTTP_OK, [], $channel);
        $response = \json_decode((string)$response);
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
        $channel = Channel::factory()->create();
        $channel->user = \Str::random(10);
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
        ]);
        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = new InfoResponse('', InfoResponse::HTTP_OK, [], $channel);
        $response = \json_decode((string)$response);
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
                            'value' => $character->handle,
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
