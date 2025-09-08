<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\ChannelType;
use App\Events\IrcMessageReceived;
use App\Events\RollEvent;
use App\Models\Channel;
use App\Rolls\Generic;
use App\Rolls\Roll;
use Error;
use Nwidart\Modules\Facades\Module;

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
     */
    public function handle(IrcMessageReceived $event): bool
    {
        $args = explode(' ', $event->content);
        $this->irc_channel = $event->channel->getName();
        $this->irc_server = $event->client->getConnection()->getServer();

        $channel = $this->getChannel();
        $channel->user = $event->user->nick;
        $channel->username = $event->user->nick;

        // See if the requested roll is XdY or something similar.
        if (1 === preg_match('/\d+d\d+/i', $args[0])) {
            $roll = new Generic($event->content, $channel->username, $channel);
            $event->client->say($this->irc_channel, $roll->forIrc());
            RollEvent::dispatch($roll, $channel);
            return true;
        }

        if (isset($channel->system) && null !== Module::find($channel->system)) {
            if (is_numeric($args[0])) {
                $class = sprintf(
                    '\\Modules\\%s\\Rolls\\Number',
                    ucfirst($channel->system),
                );
                try {
                    /** @var Roll $roll */
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

            $class = sprintf(
                '\\Modules\\%s\\Rolls\\%s',
                ucfirst($channel->system ?? 'Unknown'),
                ucfirst($args[0])
            );
            try {
                /** @var Roll $roll */
                $roll = new $class($event->content, $channel->user, $channel);
                $event->client->say($this->irc_channel, $roll->forIrc());

                if ('help' !== $args[0]) {
                    RollEvent::dispatch($roll, $channel);
                }
                return true;
            } catch (Error) {
                // Again, ignore errors, they might want a generic command.
            }
        }

        // Try generic rolls.
        try {
            $class = sprintf('\\App\\Rolls\\%s', ucfirst($args[0]));
            /** @var Roll $roll */
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

        return $channel ?? new Channel([
            'channel_id' => $this->irc_channel,
            'server_id' => $this->irc_server,
            'type' => ChannelType::Irc,
        ]);
    }
}
