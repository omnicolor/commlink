<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses;

use App\Http\Responses\InfoResponse;
use App\Models\Channel;
use App\Models\ChatUser;
use App\Models\User;

/**
 * Tests for getting info about the channel.
 * @covers \App\Http\Responses\InfoResponse
 * @group slack
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
        $user = User::factory()->create();
        $channel = Channel::factory()->make([
            'channel_id' => 'channel id',
            'server_id' => 'server id',
            'system' => 'shadowrun5e',
        ]);
        $channel->user = \Str::random(10);
        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => $channel->type,
            'user_id' => $user->id,
            'verified' => true,
        ]);

        $response = new InfoResponse('', InfoResponse::HTTP_OK, [], $channel);
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
                            'value' => 'Shadowrun 5th Edition',
                            'short' => true,
                        ],
                    ],
                ],
            ],
            $response->attachments
        );
    }
}
