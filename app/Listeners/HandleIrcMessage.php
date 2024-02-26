<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\IrcMessageReceived;
use App\Events\RollEvent;
use App\Models\Channel;
use App\Rolls\Generic;
use App\Rolls\Roll;
use Error;

use function explode;
use function is_numeric;
use function preg_match;
use function sprintf;
use function ucfirst;

class HandleIrcMessage
{
    protected string $irc_channel;
    protected string $irc_server;

    /**
     * Handle the event.
     * @param IrcMessageReceived $event
     * @psalm-suppress PossiblyUnusedMethod
     * @return bool
     */
    public function handle(IrcMessageReceived $event): bool
    {
        $args = explode(' ', $event->content);
        $this->irc_channel = $event->channel->getName();
        $this->irc_server = $event->client->getConnection()->getServer();

        $channel = $this->getChannel();
        $channel->user = $channel->username = $event->user->nick;

        // See if the requested roll is XdY or something similar.
        if (1 === preg_match('/\d+d\d+/i', $args[0])) {
            $roll = new Generic($event->content, $channel->username, $channel);
            $event->client->say($this->irc_channel, $roll->forIrc());
            RollEvent::dispatch($roll, $channel);
            return true;
        }

        // See if the roll is just a number, and if there's a number-only
        // handler for the registered system.
        if (is_numeric($args[0]) && null !== $channel->system) {
            try {
                $class = sprintf(
                    '\\App\Rolls\\%s\\Number',
                    ucfirst($channel->system),
                );
                /** @var Roll */
                $roll = new $class(
                    $event->content,
                    $channel->user,
                    $channel,
                    $event,
                );

                $event->client->say($this->irc_channel, $roll->forIrc());
                RollEvent::dispatch($roll, $channel);
                return true;
            } catch (Error) { // @codeCoverageIgnore
                // Ignore errors here, they might want a generic command.
            }
        }

        // Try system-specific rolls that aren't numeric.
        try {
            $class = sprintf(
                '\\App\\Rolls\\%s\\%s',
                ucfirst($channel->system ?? 'Unknown'),
                ucfirst($args[0])
            );
            /** @var Roll */
            $roll = new $class($event->content, $channel->user, $channel);
            $event->client->say($this->irc_channel, $roll->forIrc());

            if ('help' !== $args[0]) {
                RollEvent::dispatch($roll, $channel);
            }
            return true;
        } catch (Error) {
            // Again, ignore errors, they might want a generic command.
        }

        // Try generic rolls.
        try {
            $class = sprintf('\\App\\Rolls\\%s', ucfirst($args[0]));
            /** @var Roll */
            $roll = new $class(
                $event->content,
                $channel->username,
                $channel,
                $event
            );
            $event->client->say($this->irc_channel, $roll->forIrc());
            return true;
        } catch (Error) {
        }

        $event->client->say(
            $this->irc_channel,
            sprintf('%s: That doesn\'t appear to be a valid command!', $channel->user),
        );
        return true;
    }

    protected function getChannel(): Channel
    {
        $channel = Channel::irc()
            ->where('channel_id', $this->irc_channel)
            ->where('server_id', $this->irc_server)
            ->first();
        if (null !== $channel) {
            return $channel;
        }

        return new Channel([
            'channel_id' => $this->irc_channel,
            'server_id' => $this->irc_server,
            'type' => Channel::TYPE_IRC,
        ]);
    }
}
