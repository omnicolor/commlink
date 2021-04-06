<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\DiscordMessageReceived;

class HandleDiscordListener
{
    /**
     * Handle the event.
     * @param DiscordMessageReceived $event
     */
    public function handle($event): void
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
    }
}
