<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Responses;

use App\Http\Responses\InfoResponse;
use App\Models\Slack\Channel;

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
    public function testToArrayUnregistered(): void
    {
        $channel = new Channel([
            'channel' => 'channel id',
            'team' => 'server id',
            'user' => 'user id',
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
                            'value' => 'user id',
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
    public function testToArrayRegistered(): void
    {
        $channel = new Channel([
            'channel' => 'channel id',
            'team' => 'server id',
            'user' => 'user id',
            'system' => 'shadowrun5e',
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
                            'value' => 'user id',
                            'short' => true,
                        ],
                        (object)[
                            'title' => 'System',
                            'value' => 'shadowrun5e',
                            'short' => true,
                        ],
                    ],
                ],
            ],
            $response->attachments
        );
    }
}
