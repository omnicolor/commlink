<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\DiscordMessageReceived;

class HandleDiscordListener
{
    /**
     * Handle the event.
     * @param DiscordMessageReceived $event
     * @return bool
     */
    public function handle(DiscordMessageReceived $event): bool
    {
        \Log::info(sprintf(
            'Listener: Received Discord command from %s (%s.%s): %s',
            $event->user->tag,
            $event->server->name,
            // @phpstan-ignore-next-line
            $event->channel->name,
            $event->content
        ));
        $event->channel->send('Hello there!');
        return true;
    }
}
