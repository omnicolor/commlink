<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses;

use App\Exceptions\SlackException;
use App\Http\Responses\HelpResponse;
use App\Models\Channel;
use App\Models\ChatUser;

/**
 * Tests for unregistered HelpResponses.
 * @group slack
 * @medium
 */
final class HelpResponseTest extends \Tests\TestCase
{
    /**
     * Test not including a channel.
     * @test
     */
    public function testNoChannel(): void
    {
        self::expectException(SlackException::class);
        self::expectExceptionMessage('Channel is required');
        new HelpResponse('help', HelpResponse::HTTP_OK, []);
    }

    /**
     * Test the titles for a `/roll help` command in an unregistered channel
     * without a linked chat user.
     * @test
     */
    public function testTitlesUnregisteredNotLinked(): void
    {
        $channel = new Channel();
        $response = new HelpResponse('', 200, [], $channel);
        $text = (string)$response;
        $response = \json_decode($text);
        self::assertSame('ephemeral', $response->response_type);
        self::assertCount(4, $response->attachments);
        self::assertSame(
            \sprintf('About %s', config('app.name')),
            $response->attachments[0]->title
        );
        self::assertSame('Supported Systems', $response->attachments[1]->title);
        self::assertSame(
            'Note for unregistered users:',
            $response->attachments[2]->title
        );
        self::assertSame(
            'Commands for unregistered channels:',
            $response->attachments[3]->title
        );
    }

    /**
     * Test the three titles for a `/roll help` command in an unregistered
     * channel with a linked chat user.
     * @test
     */
    public function testTitlesUnregisteredLinked(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make();
        $channel->user = \Str::random(10);
        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => $channel->type,
            'verified' => true,
        ]);
        $response = new HelpResponse('', 200, [], $channel);
        $text = (string)$response;
        $response = \json_decode($text);
        self::assertSame('ephemeral', $response->response_type);
        self::assertCount(3, $response->attachments);
        self::assertSame(
            \sprintf('About %s', config('app.name')),
            $response->attachments[0]->title
        );
        self::assertSame('Supported Systems', $response->attachments[1]->title);
        self::assertSame(
            'Commands for unregistered channels:',
            $response->attachments[2]->title
        );
    }
}
