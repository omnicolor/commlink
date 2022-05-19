<?php

declare(strict_types=1);

namespace App\Http\Responses\Discord;

use App\Events\DiscordMessageReceived;
use App\Models\Channel;
use App\Models\Character;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use Discord\Parts\Channel\Channel as TextChannel;

/**
 * Handle a user requesting to link a character to this Discord channel.
 */
class LinkResponse
{
    protected ?Channel $channel;
    protected TextChannel $textChannel;
    protected string $message = '';

    /**
     * Construct a new instance.
     * @param DiscordMessageReceived $event
     */
    public function __construct(protected DiscordMessageReceived $event)
    {
        $arguments = \explode(' ', trim($this->event->content));
        if (2 !== \count($arguments)) {
            $this->sendMissingArgumentError();
            return;
        }

        $this->textChannel = $this->event->channel;
        $this->channel = Channel::discord()
            ->where('channel_id', $this->textChannel->id)
            ->where('server_id', $this->event->server->id)
            ->first();
        if (null === $this->channel) {
            $this->sendChannelNotRegisteredError();
            return;
        }

        $this->channel->user = (string)optional($this->event->user)->id;

        /** @var ?ChatUser */
        $chatUser = $this->channel->getChatUser();
        if (null === $chatUser) {
            $this->sendMustRegisterError();
            return;
        }

        if (null !== $this->channel->character()) {
            $this->sendAlreadyLinkedError($this->channel->character());
            return;
        }

        $characterId = $arguments[1];
        $character = Character::find($characterId);
        if (null === $character) {
            $this->sendNotFoundError();
            return;
        }

        $user = $chatUser->user;
        if (null === $user || $character->owner !== $user->email) {
            $this->sendOtherOwnerError();
            return;
        }

        if ($this->channel->system !== $character->system) {
            $this->sendWrongSystemError($character);
            return;
        }

        ChatCharacter::create([
            'channel_id' => $this->channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $this->event->message->reply(\sprintf(
            'You have linked %s to this channel.',
            (string)$character
        ));
    }

    /**
     * Format the response for Discord.
     * @return string
     */
    public function __toString(): string
    {
        return $this->message;
    }

    protected function sendChannelNotRegisteredError(): void
    {
        $systems = [];
        foreach (config('app.systems') as $code => $name) {
            $systems[] = \sprintf('%s (%s)', $code, $name);
        }
        $this->message = 'This channel must be registered for a system before '
            . 'characters can be linked. Type `/roll register <system>`, where '
            . '<system> is one of: ' . \implode(', ', $systems);
    }

    protected function sendMissingArgumentError(): void
    {
        $this->message = 'To link a character, use `link <characterId>`.';
    }

    protected function sendMustRegisterError(): void
    {
        $this->message = \sprintf(
            'You must have already created an account on %s (%s) and linked it '
                . 'to this server before you can link a character.',
            config('app.name'),
            config('app.url') . '/settings',
        );
    }

    protected function sendAlreadyLinkedError(Character $character): void
    {
        $this->event->message->reply(\sprintf(
            'It looks like you\'ve already linked "%s" to this channel.',
            (string)$character
        ));
    }

    protected function sendNotFoundError(): void
    {
        $this->event->message->reply(
            'Unable to find one of your characters with that ID.'
        );
    }

    protected function sendOtherOwnerError(): void
    {
        $this->event->message->reply('You don\'t own that character.');
    }

    protected function sendWrongSystemError(Character $character): void
    {
        $systems = config('app.systems');
        $this->event->message->reply(\sprintf(
            '%s is a %s character. This channel is playing %s.',
            (string)$character,
            $systems[$character->system] ?? 'Unknown',
            // @phpstan-ignore-next-line
            $systems[$this->channel->system] ?? 'Unknown',
        ));
    }
}
