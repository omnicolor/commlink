<?php

declare(strict_types=1);

namespace App\Http\Responses\Discord;

use App\Events\ChannelLinked;
use App\Events\DiscordMessageReceived;
use App\Models\Channel;
use Discord\Parts\Channel\Channel as TextChannel;

/**
 * Handle a user requesting to link a Discord channel to Commlink.
 */
class RegisterResponse
{
    protected const MIN_NUM_ARGUMENTS = 2;

    protected string $message = '';

    /**
     * Construct a new instance.
     * @param DiscordMessageReceived $event
     */
    public function __construct(protected DiscordMessageReceived $event)
    {
        $arguments = \explode(' ', trim($this->event->content));
        $systems = config('app.systems');
        if (self::MIN_NUM_ARGUMENTS !== \count($arguments)) {
            $this->sendMissingArgumentError($systems);
            return;
        }

        $system = $arguments[1];
        if (!\array_key_exists($system, $systems)) {
            $this->sendInvalidSystemError($system, $systems);
            return;
        }

        $discordChannel = $event->channel;
        $channel = Channel::discord()
            ->where('channel_id', $discordChannel->id)
            ->where('server_id', $event->server->id)
            ->first();

        if (null !== $channel) {
            $this->sendAlreadyRegisteredError((string)$channel->system);
            return;
        }

        $channel = $this->createNewChannel($discordChannel, $system);
        $chatUser = $channel->getChatUser();
        if (null === $chatUser) {
            $this->sendMustRegisterError();
            return;
        }

        $channel->fill([
            'channel_name' => $channel->getDiscordChannelName($channel->channel_id),
            'server_name' => $channel->getDiscordServerName($channel->server_id),
            // @phpstan-ignore-next-line
            'registered_by' => $chatUser->user->id,
            'webhook' => $channel->createDiscordWebhook($channel->channel_id),
        ]);
        $channel->save();

        $event->channel->sendMessage(\sprintf(
            '%s has registered this channel for the "%s" system.',
            $channel->username,
            $systems[$system],
        ));

        ChannelLinked::dispatch($channel);
    }

    /**
     * Format the response for Discord.
     * @return string
     */
    public function __toString(): string
    {
        return $this->message;
    }

    /**
     * Create a new Commlink channel for the Discord channel.
     * @param TextChannel $discordChannel
     * @param string $system
     * @return Channel
     */
    protected function createNewChannel(
        TextChannel $discordChannel,
        string $system
    ): Channel {
        $channel = new Channel([
            'channel_id' => $discordChannel->id,
            'server_id' => $this->event->server->id,
            'system' => $system,
            'type' => Channel::TYPE_DISCORD,
        ]);

        $channel->user = (string)$this->event->user->id;
        $channel->username = optional($this->event->user)->displayname;
        return $channel;
    }

    /**
     * Let the user know they need another argument to register a channel.
     * @param array<string, string> $systems
     */
    protected function sendMissingArgumentError(array $systems): void
    {
        $this->event->message->reply(\sprintf(
            'To register a channel, use `register [system]`, where system is a '
                . 'system code: %s',
            \implode(', ', \array_keys($systems))
        ));
    }

    /**
     * Let the user know they chose an invalid system.
     * @param string $system
     * @param array<string, string> $systems
     */
    protected function sendInvalidSystemError(
        string $system,
        array $systems
    ): void {
        $this->event->message->reply(\sprintf(
            '"%s" is not a valid system code. Use `register <system>`, '
                . 'where system is one of: %s',
            $system,
            \implode(', ', \array_keys($systems))
        ));
    }

    /**
     * Let the user know the channel is already registered.
     * @param string $system
     */
    protected function sendAlreadyRegisteredError(string $system): void
    {
        $this->event->message->reply(
            \sprintf('This channel is already registered for "%s"', $system)
        );
    }

    /**
     * Let the user know they need to link their user first.
     */
    protected function sendMustRegisterError(): void
    {
        $this->event->message->reply(\sprintf(
            'You must have already created an account on %s (%s) and linked it '
                . 'to this server before you can register a channel to a '
                . 'specific system.',
            config('app.name'),
            config('app.url') . '/settings',
        ));
    }
}
