<?php

declare(strict_types=1);

namespace App\Http\Responses\Discord;

use App\Events\DiscordMessageReceived;

class InfoResponse
{
    /**
     * Discord message received.
     * @var DiscordMessageReceived
     */
    protected DiscordMessageReceived $event;

    /**
     * Construct a new instance.
     * @param DiscordMessageReceived $event
     */
    public function __construct(DiscordMessageReceived $event)
    {
        $this->event = $event;
    }

    /**
     * Format the response for Discord.
     * @return string
     */
    public function __toString(): string
    {
        /** @var \CharlotteDunois\Yasmin\Models\TextChannel */
        $channel = $this->event->channel;
        return '**Debugging info**' . \PHP_EOL
            . 'User Tag: ' . $this->event->user->tag . \PHP_EOL
            . 'User ID: ' . $this->event->user->id . \PHP_EOL
            . 'Server Name: ' . $this->event->server->name . \PHP_EOL
            . 'Server ID: ' . $this->event->server->id . \PHP_EOL
            . 'Channel Name: ' . $channel->name . \PHP_EOL
            . 'Channel ID: ' . $channel->id . \PHP_EOL
            . 'System: ' . 'Unregistered' . \PHP_EOL
            . 'Character: ' . 'Unlinked' . \PHP_EOL;
    }
}
