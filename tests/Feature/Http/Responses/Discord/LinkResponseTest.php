<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses\Discord;

use App\Events\DiscordMessageReceived;
use App\Http\Responses\Discord\LinkResponse;
use App\Models\Channel;
use App\Models\Character;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\User;
use Discord\Discord;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

/**
 * @group discord
 */
#[Medium]
final class LinkResponseTest extends TestCase
{
    /**
     * Test trying to link a character without sending their ID.
     */
    public function testLinkWithoutId(): void
    {
        $messageMock = $this->createDiscordMessageMock('/roll link');
        $event = new DiscordMessageReceived(
            $messageMock,
            self::createStub(Discord::class)
        );

        $expected = 'To link a character, use `link <characterId>`.';
        self::assertSame(
            'To link a character, use `link <characterId>`.',
            (string)(new LinkResponse($event))
        );
    }

    /**
     * Test trying to link to an unregistered channel.
     */
    public function testLinkUnregisteredChannel(): void
    {
        $messageMock = $this->createDiscordMessageMock('/roll link 123');
        $event = new DiscordMessageReceived(
            $messageMock,
            self::createStub(Discord::class)
        );

        $systems = [];
        foreach (config('app.systems') as $code => $name) {
            $systems[] = \sprintf('%s (%s)', $code, $name);
        }
        $expected = 'This channel must be registered for a system before '
            . 'characters can be linked. Type `/roll register <system>`, where '
            . '<system> is one of: ' . \implode(', ', $systems);
        self::assertSame($expected, (string)(new LinkResponse($event)));
    }

    /**
     * Test trying to link a character without having a registered Commlink
     * user.
     */
    public function testLinkWithoutCommlinkUser(): void
    {
        $expected = \sprintf(
            'You must have already created an account on %s (%s) and '
                . 'linked it to this server before you can link a character.',
            config('app.name'),
            config('app.url') . '/settings',
        );

        $messageMock = $this->createDiscordMessageMock('/roll link 123');
        $event = new DiscordMessageReceived(
            $messageMock,
            self::createStub(Discord::class)
        );

        Channel::factory()->create([
            'channel_id' => $event->channel->id,
            'server_id' => $event->server->id,
            'type' => Channel::TYPE_DISCORD,
        ]);

        self::assertSame(
            'You must have already created an account on Commlink - Test '
                . '(http://localhost/settings) and linked it to this server '
                . 'before you can link a character.',
            (string)(new LinkResponse($event))
        );
    }

    /**
     * Test trying to link a character if there's already a character linked to
     * the channel.
     */
    public function testLinkCharacterAlreadyLinked(): void
    {
        /** @var Character */
        $alreadyLinkedCharacter = Character::factory()->create([
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        $expected = \sprintf(
            'It looks like you\'ve already linked "%s" to this channel.',
            (string)$alreadyLinkedCharacter
        );
        $messageMock = $this->createDiscordMessageMock('/roll link 123');
        // @phpstan-ignore-next-line
        $messageMock->expects(self::once())
            ->method('reply')
            ->with($expected);
        $event = new DiscordMessageReceived(
            $messageMock,
            self::createStub(Discord::class)
        );

        $channel = Channel::factory()->create([
            'channel_id' => $event->channel->id,
            'server_id' => $event->server->id,
            'type' => Channel::TYPE_DISCORD,
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => optional($event->user)->id,
            'server_id' => $event->server->id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'verified' => true,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel,
            'character_id' => $alreadyLinkedCharacter->id,
            'chat_user_id' => $chatUser,
        ]);

        self::assertSame('', (string)(new LinkResponse($event)));
        $alreadyLinkedCharacter->delete();
    }

    /**
     * Test trying to link an invalid ID to the channel.
     */
    public function testLinkInvalidCharacter(): void
    {
        $expected = 'Unable to find one of your characters with that ID.';
        $messageMock = $this->createDiscordMessageMock('/roll link 123');
        // @phpstan-ignore-next-line
        $messageMock->expects(self::once())
            ->method('reply')
            ->with($expected);
        $event = new DiscordMessageReceived(
            $messageMock,
            self::createStub(Discord::class)
        );

        $channel = Channel::factory()->create([
            'channel_id' => $event->channel->id,
            'server_id' => $event->server->id,
            'type' => Channel::TYPE_DISCORD,
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => optional($event->user)->id,
            'server_id' => $event->server->id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'verified' => true,
        ]);

        self::assertSame('', (string)(new LinkResponse($event)));
    }

    /**
     * Test trying to link someone else's character to the channel.
     */
    public function testLinkCharacterNotYours(): void
    {
        /** @var Character */
        $character = Character::factory()->create([
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        $expected = 'You don\'t own that character.';
        $messageMock = $this->createDiscordMessageMock(\sprintf(
            '/roll link %s',
            $character->id
        ));
        // @phpstan-ignore-next-line
        $messageMock->expects(self::once())
            ->method('reply')
            ->with($expected);
        $event = new DiscordMessageReceived(
            $messageMock,
            self::createStub(Discord::class)
        );

        $channel = Channel::factory()->create([
            'channel_id' => $event->channel->id,
            'server_id' => $event->server->id,
            'type' => Channel::TYPE_DISCORD,
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => optional($event->user)->id,
            'server_id' => $event->server->id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'verified' => true,
        ]);

        self::assertSame('', (string)(new LinkResponse($event)));

        $character->delete();
    }

    /**
     * Test trying to link one of your characters that is for a different
     * system than the channel is registered for.
     */
    public function testLinkCharacterOtherSystem(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Character */
        $character = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'capers',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        $expected = \sprintf(
            '%s is a Capers character. This channel is playing Shadowrun 5th Edition.',
            (string)$character,
        );
        $messageMock = $this->createDiscordMessageMock(\sprintf(
            '/roll link %s',
            $character->id
        ));
        // @phpstan-ignore-next-line
        $messageMock->expects(self::once())
            ->method('reply')
            ->with($expected);
        $event = new DiscordMessageReceived(
            $messageMock,
            self::createStub(Discord::class)
        );

        $channel = Channel::factory()->create([
            'channel_id' => $event->channel->id,
            'server_id' => $event->server->id,
            'system' => 'shadowrun5e',
            'type' => Channel::TYPE_DISCORD,
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => optional($event->user)->id,
            'server_id' => $event->server->id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'user_id' => $user,
            'verified' => true,
        ]);

        self::assertSame('', (string)(new LinkResponse($event)));

        $character->delete();
    }

    /**
     * Test linking a character to the channel.
     */
    public function testLinkCharacter(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Character */
        $character = Character::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        $expected = \sprintf(
            'You have linked %s to this channel.',
            (string)$character,
        );
        $messageMock = $this->createDiscordMessageMock(\sprintf(
            '/roll link %s',
            $character->id
        ));
        // @phpstan-ignore-next-line
        $messageMock->expects(self::once())
            ->method('reply')
            ->with($expected);
        $event = new DiscordMessageReceived(
            $messageMock,
            self::createStub(Discord::class)
        );

        $channel = Channel::factory()->create([
            'channel_id' => $event->channel->id,
            'server_id' => $event->server->id,
            'system' => $character->system,
            'type' => Channel::TYPE_DISCORD,
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => optional($event->user)->id,
            'server_id' => $event->server->id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'user_id' => $user,
            'verified' => true,
        ]);

        self::assertSame('', (string)(new LinkResponse($event)));

        $character->delete();
    }
}
