<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses\Slack;

use App\Http\Responses\Slack\LinkResponse;
use App\Models\Channel;
use App\Models\Character;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\User;
use Illuminate\Support\Str;
use Omnicolor\Slack\Exceptions\SlackException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function sha1;
use function sprintf;

#[Group('slack')]
#[Medium]
final class LinkResponseTest extends TestCase
{
    /**
     * Test trying to link a character if the user has no Commlink account.
     */
    public function testResponseNoCommlink(): void
    {
        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'You must have already created an account on '
                . '<http://localhost|Commlink - Test> and linked it to this '
                . 'server before you can register a channel to a specific '
                . 'system.'
        );
        new LinkResponse(content: 'link deadb33f', channel: new Channel());
    }

    /**
     * Test trying to link a character if the user has an unverified Commlink
     * account.
     */
    public function testWithUnverifiedCommlinkAccount(): void
    {
        $channel = Channel::factory()->create();
        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'verified' => false,
        ]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'You must have already created an account on '
                . '<http://localhost|Commlink - Test> and linked it to this '
                . 'server before you can register a channel to a specific '
                . 'system.'
        );
        new LinkResponse(content: 'link deadb33f', channel: $channel);
    }

    /**
     * Test trying to link a character if the user has a Commlink account and
     * has already linked a character to the channel.
     */
    public function testResponseAlreadyLinked(): void
    {
        $user = User::factory()->create();
        $character = Character::factory()->create([
            'owner' => $user->email->address,
        ]);
        $channel = Channel::factory()->create([
            'system' => $character->system,
            'type' => Channel::TYPE_SLACK,
        ]);
        $channel->user = Str::random(10);
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'user_id' => $user,
            'verified' => true,
        ]);
        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'This channel is already linked to a character.'
        );
        new LinkResponse(content: 'link deadb33f', channel: $channel);
        $character->delete();
    }

    /**
     * Test trying to link an ID for a character that doesn't exist.
     */
    public function testLinkNotFoundCharacter(): void
    {
        $user = User::factory()->create();
        $channel = Channel::factory()->create(['type' => Channel::TYPE_SLACK]);
        $channel->user = Str::random(10);
        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'user_id' => $user,
            'verified' => true,
        ]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'Unable to find one of your characters with that ID.'
        );
        new LinkResponse(
            content: sprintf('link %s', sha1(Str::random(10))),
            channel: $channel,
        );
    }

    /**
     * Test trying to link a different user's Character to the channel.
     */
    public function testLinkAnotherUsersCharacter(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $character = Character::factory()->create([
            '_id' => sha1(Str::random(10)),
            'owner' => $otherUser->email->address,
        ]);
        $channel = Channel::factory()->create([
            'system' => $character->system,
            'type' => Channel::TYPE_SLACK,
        ]);
        $channel->user = Str::random(10);
        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'user_id' => $user,
            'verified' => true,
        ]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage('You don\'t own that character.');
        new LinkResponse(
            content: sprintf('link %s', $character->_id),
            channel: $channel,
        );
        $character->delete();
    }

    /**
     * Test trying to link a character to the channel that isn't the right
     * system.
     */
    public function testLinkWrongSystem(): void
    {
        $user = User::factory()->create();
        $character = Character::factory()->create([
            '_id' => sha1(Str::random(10)),
            'owner' => $user->email->address,
            'system' => 'shadowrun5e',
        ]);
        $channel = Channel::factory()->create([
            'system' => 'expanse',
            'type' => Channel::TYPE_SLACK,
        ]);
        $channel->user = Str::random(10);
        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'user_id' => $user,
            'verified' => true,
        ]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(sprintf(
            '%s is a Shadowrun 5th Edition character. '
                . 'This channel is playing The Expanse.',
            (string)$character
        ));
        new LinkResponse(
            content: sprintf('link %s', $character->_id),
            channel: $channel,
        );

        $character->delete();
    }

    /**
     * Test linking a character of the correct system.
     */
    public function testLinkCharacter(): void
    {
        $user = User::factory()->create();
        $character = Character::factory()->create([
            'owner' => $user->email->address,
        ]);
        $channel = Channel::factory()->create([
            'system' => $character->system,
            'type' => Channel::TYPE_SLACK,
        ]);
        $channel->user = Str::random(10);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'user_id' => $user->id,
            'verified' => true,
        ]);
        $response = new LinkResponse(
            content: sprintf('link %s', $character->_id),
            channel: $channel,
        );
        $response = json_decode((string)$response);
        self::assertSame(
            sprintf('You have linked %s to this channel.', (string)$character),
            $response->attachments[0]->text
        );
        self::assertDatabaseHas(
            'chat_characters',
            [
                'channel_id' => $channel->id,
                'character_id' => $character->id,
                'chat_user_id' => $chatUser->id,
            ]
        );
        $character->delete();
    }
}
