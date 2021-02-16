<?php

declare(strict_types=1);

namespace Tests\Unit\Responses;

use App\Exceptions\SlackException;
use App\Http\Responses\RegisterResponse;
use App\Models\Slack\Channel;

/**
 * Tests for registering a channel in Slack.
 * @covers \App\Http\Responses\RegisterResponse
 * @group slack
 */
final class RegisterResponseTest extends \Tests\TestCase
{
    /**
     * Test trying to register a channel without passing in the channel.
     * @test
     */
    public function testChannelMissing(): void
    {
        self::expectException(SlackException::class);
        self::expectExceptionMessage('Channel doesn\'t exist');
        $response = new RegisterResponse();
    }

    /**
     * Test registering a channel that's already registered.
     * @test
     */
    public function testChannelAlreadyRegistered(): void
    {
        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'This channel is already registered for "shadowrun5e"'
        );
        $response = new RegisterResponse(
            '',
            RegisterResponse::HTTP_OK,
            [],
            new Channel(['system' => 'shadowrun5e'])
        );
    }

    /**
     * Test registering a channel without specifying the system.
     * @test
     */
    public function testRegisterWithoutSystem(): void
    {
        self::expectException(SlackException::class);
        self::expectExceptionMessage(sprintf(
            'To register a channel, use `register [system]`, where system '
                . 'is a system code: %s',
            implode(', ', array_keys(config('app.systems')))
        ));
        $response = new RegisterResponse(
            'register',
            RegisterResponse::HTTP_OK,
            [],
            new Channel()
        );
    }

    /**
     * Test registering a channel to an invalid system.
     * @test
     */
    public function testRegisterInvalidSystem(): void
    {
        self::expectException(SlackException::class);
        self::expectExceptionMessage(sprintf(
            '"%s" is not a valid system code. Use `register [system]`, '
                . 'where system is: %s',
            'invalid',
            implode(', ', array_keys(config('app.systems')))
        ));
        $response = new RegisterResponse(
            'register invalid',
            RegisterResponse::HTTP_OK,
            [],
            new Channel()
        );
    }

    /**
     * Test registering a channel to a valid system.
     * @test
     */
    public function testRegister(): void
    {
        $channel = Channel::create([
            'channel' => 'channel-id',
            'team' => 'team-id',
            'username' => 'Testing',
        ]);
        $response = new RegisterResponse(
            sprintf('register %s', key(config('app.systems'))),
            RegisterResponse::HTTP_OK,
            [],
            $channel
        );
        self::assertSame(key(config('app.systems')), $channel->system);
        $channel->delete();
    }
}
