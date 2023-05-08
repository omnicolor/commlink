<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\IrcMessageReceived;
use App\Events\RollEvent;
use App\Models\Channel;
use App\Rolls\Generic;
use App\Rolls\Roll;
use Error;

class HandleIrcMessage
{
    protected string $ircChannel;
    protected string $ircServer;

    /**
     * Handle the event.
     * @param IrcMessageReceived $event
     * @psalm-suppress PossiblyUnusedMethod
     * @return bool
     */
    public function handle(IrcMessageReceived $event): bool
    {
        $args = \explode(' ', $event->content);
        $this->ircChannel = $event->channel->getName();
        $this->ircServer = $event->client->getConnection()->getServer();

        $channel = $this->getChannel();
        $channel->user = $channel->username = $event->user;

        // See if the requested roll is XdY or something similar.
        if (1 === \preg_match('/\d+d\d+/i', $args[0])) {
            $roll = new Generic($event->content, $channel->username, $channel);
            $event->client->say($this->ircChannel, $roll->forIrc());
            RollEvent::dispatch($roll, $channel);
            return true;
        }

        // Try generic rolls.
        try {
            $class = \sprintf('\\App\\Rolls\\%s', \ucfirst($args[0]));
            /** @var Roll */
            $roll = new $class(
                $event->content,
                $channel->username,
                $channel,
                $event
            );
            $event->client->say($this->ircChannel, $roll->forIrc());
            return true;
        } catch (Error) {
        }

        $event->client->say(
            $this->ircChannel,
            \sprintf('@%s: That doesn\'t appear to be a valid command!', $channel->user),
        );
        return true;
    }

    protected function getChannel(): Channel
    {
        $channel = Channel::irc()
            ->where('channel_id', $this->ircChannel)
            ->where('server_id', $this->ircServer)
            ->first();
        if (null !== $channel) {
            return $channel;
        }

        return new Channel([
            'channel_id' => $this->ircChannel,
            'server_id' => $this->ircServer,
            'type' => Channel::TYPE_IRC,
        ]);
    }
}
