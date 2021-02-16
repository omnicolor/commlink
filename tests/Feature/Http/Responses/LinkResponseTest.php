<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses;

use App\Exceptions\SlackException;
use App\Http\Responses\LinkResponse;
use App\Models\Slack\Channel;
use App\Models\SlackLink;
use App\Models\Shadowrun5E\Character;
use App\Models\User;
use Str;

/**
 * Tests for linking a character to a Slack channel.
 * @covers \App\Http\Responses\LinkResponse
 * @group slack
 */
final class LinkResponseTest extends \Tests\TestCase
{
    /**
     * Characters we're testing with.
     * @var array<int, Character>
     */
    protected array $characters = [];

    /**
     * Faker instance.
     * @var \Faker\Generator
     */
    protected static \Faker\Generator $faker;

    /**
     * Set up the test suite.
     */
    public static function setUpBeforeClass(): void
    {
        self::$faker = \Faker\Factory::create();
    }

    /**
     * Clean up.
     */
    public function tearDown(): void
    {
        foreach ($this->characters as $key => $character) {
            $character->delete();
            unset($this->characters[$key]);
        }
        parent::tearDown();
    }

    /**
     * Test trying to link without a channel.
     * @test
     */
    public function testLinkNoChannel(): void
    {
        self::expectException(SlackException::class);
        self::expectExceptionMessage('Channel doesn\'t exist.');
        new LinkResponse();
    }

    /**
     * Test trying to link a channel that hasn't been registered yet.
     * @test
     */
    public function testLinkUnregisteredChannel(): void
    {
        $channel = new Channel([
            'channel' => Str::random(10),
            'team' => Str::random(10),
        ]);
        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'This channel isn\'t registered for a system yet. Use `register '
                . '[system]` before trying to link characters.'
        );
        new LinkResponse('', LinkResponse::HTTP_OK, [], $channel);
    }

    /**
     * Test trying to link a character without giving the character ID.
     * @test
     */
    public function testLinkWithoutCharacterID(): void
    {
        $channel = new Channel([
            'channel' => Str::random(10),
            'system' => 'shadowrun5e',
            'team' => Str::random(10),
        ]);
        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'To link a character to this channel, use `link [characterId]`.'
        );
        new LinkResponse('link', LinkResponse::HTTP_OK, [], $channel);
    }

    /**
     * Test trying to link a character without setting up the channel in
     * Commlink.
     * @test
     */
    public function testLinkWithoutSlackLink(): void
    {
        $channel = new Channel([
            'channel' => Str::random(10),
            'system' => 'shadowrun5e',
            'team' => Str::random(10),
        ]);
        self::expectException(SlackException::class);
        self::expectExceptionMessage(sprintf(
            'It doesn\'t look like you\'ve registered this channel with '
                . '<%s|Commlink>. You need to add this Slack Team (%s) and '
                . 'your Slack User (%s) before you can link a character.',
            config('app.url'),
            $channel->team,
            $channel->user
        ));
        new LinkResponse('link deadb33f', LinkResponse::HTTP_OK, [], $channel);
    }

    /**
     * Test trying to link a character that doesn't exist in Commlink.
     * @test
     */
    public function testLinkWithoutCharacter(): void
    {
        $channel = new Channel([
            'channel' => Str::random(10),
            'system' => 'shadowrun5e',
            'team' => Str::random(10),
        ]);
        $channel->user = Str::random(10);
        $user = User::factory()->create();
        $slackLink = SlackLink::factory([
            'slack_team' => $channel->team,
            'slack_user' => $channel->user,
            'user_id' => $user->id,
        ])->create();
        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'Could not find a character with that ID.'
        );
        new LinkResponse('link deadb33f', LinkResponse::HTTP_OK, [], $channel);
    }

    /**
     * Test trying to link a character that's owned by someone else.
     * @test
     */
    public function testLinkCharacterOwnedByOther(): void
    {
        $channel = new Channel([
            'channel' => Str::random(10),
            'system' => 'shadowrun5e',
            'team' => Str::random(10),
        ]);
        $channel->user = Str::random(10);
        $user = User::factory()->create();
        $character = $this->characters[] = Character::factory()
            ->create(['owner' => self::$faker->email]);
        $slackLink = SlackLink::factory([
            'slack_team' => $channel->team,
            'slack_user' => $channel->user,
            'user_id' => $user->id,
        ])->create();
        self::expectException(SlackException::class);
        self::expectExceptionMessage('You don\'t own that character.');
        new LinkResponse(
            sprintf('link %s', $character->id),
            LinkResponse::HTTP_OK,
            [],
            $channel
        );
    }

    /**
     * Test trying to link a character for a system the server doesn't support.
     * @test
     */
    public function testLinkUnsupportedSystem(): void
    {
        \Config::set('app.systems', []);
        $channel = new Channel([
            'channel' => Str::random(10),
            'system' => 'shadowrun5e',
            'team' => Str::random(10),
        ]);
        $channel->user = Str::random(10);
        $user = User::factory()->create();
        $character = $this->characters[] = Character::factory()
            ->create(['owner' => $user->email]);
        $slackLink = SlackLink::factory([
            'slack_team' => $channel->team,
            'slack_user' => $channel->user,
            'user_id' => $user->id,
        ])->create();
        self::expectException(SlackException::class);
        self::expectExceptionMessage(sprintf(
            '"%s" is registered for a system (shadowrun5e) that this server '
                . 'does not support.',
            $character->handle
        ));
        new LinkResponse(
            sprintf('link %s', $character->id),
            LinkResponse::HTTP_OK,
            [],
            $channel
        );
    }

    /**
     * Test trying to link a character for one system to a channel registered
     * for a different system.
     * @test
     */
    public function testLinkDifferentSystem(): void
    {
        $channel = new Channel([
            'channel' => Str::random(10),
            'system' => 'expanse',
            'team' => Str::random(10),
        ]);
        $channel->user = Str::random(10);
        $user = User::factory()->create();
        $character = $this->characters[] = Character::factory()
            ->create(['owner' => $user->email]);
        $slackLink = SlackLink::factory([
            'slack_team' => $channel->team,
            'slack_user' => $channel->user,
            'user_id' => $user->id,
        ])->create();
        self::expectException(SlackException::class);
        self::expectExceptionMessage(sprintf(
            '"%s" is a character for Shadowrun 5th Edition, but this '
                . 'channel is registered to play The Expanse.',
            $character->handle
        ));
        new LinkResponse(
            sprintf('link %s', $character->id),
            LinkResponse::HTTP_OK,
            [],
            $channel
        );
    }

    /**
     * Test trying to link a character to a channel that already has
     * a character linked.
     * @test
     */
    public function testLinkAnotherCharacter(): void
    {
        $channel = new Channel([
            'channel' => Str::random(10),
            'system' => 'shadowrun5e',
            'team' => Str::random(10),
        ]);
        $channel->user = Str::random(10);
        $user = User::factory()->create();
        $previousCharacter = $this->characters[] = Character::factory()
            ->create();
        $newCharacter = $this->characters[] = Character::factory()
            ->create(['owner' => $user->email]);
        $slackLink = SlackLink::factory([
            'character_id' => $previousCharacter->id,
            'slack_team' => $channel->team,
            'slack_user' => $channel->user,
            'user_id' => $user->id,
        ])->create();
        self::expectException(SlackException::class);
        self::expectExceptionMessage(sprintf(
            'This channel is already linked to a character: "%s". You can '
                . 'unlink it with `/roll unlink`, then try again to link '
                . '"%s".',
            $previousCharacter->handle,
            $newCharacter->handle
        ));
        new LinkResponse(
            sprintf('link %s', $newCharacter->id),
            LinkResponse::HTTP_OK,
            [],
            $channel
        );
    }

    /**
     * Test linking a character to a channel.
     * @test
     */
    public function testLinkCharacter(): void
    {
        $channel = new Channel([
            'channel' => Str::random(10),
            'system' => 'shadowrun5e',
            'team' => Str::random(10),
        ]);
        $channel->user = Str::random(10);
        $user = User::factory()->create();
        $character = $this->characters[] = Character::factory()
            ->create(['owner' => $user->email]);
        $slackLink = SlackLink::factory([
            'slack_team' => $channel->team,
            'slack_user' => $channel->user,
            'user_id' => $user->id,
        ])->create();
        $response = new LinkResponse(
            sprintf('link %s', $character->id),
            LinkResponse::HTTP_OK,
            [],
            $channel
        );
        $response = json_decode((string)$response);

        // Test the actual response back to the user.
        self::assertSame('in_channel', $response->response_type);
        self::assertEquals(
            [
                (object)[
                    'title' => 'Linked',
                    'text' => sprintf(
                        'Character "%s" linked for %s',
                        $character->handle,
                        $channel->username
                    ),
                    'color' => LinkResponse::COLOR_SUCCESS,
                ],
            ],
            $response->attachments
        );

        // And test that it was persisted in the database.
        self::assertNull($slackLink->character());
        $slackLink->refresh();
        self::assertSame($character->handle, $slackLink->character()->handle);
    }
}
