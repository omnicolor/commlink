<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\DiscordMessageReceived;
use App\Events\RollEvent;

class HandleDiscordMessage
{
    /**
     * Handle the event.
     * @param DiscordMessageReceived $event
     * @return bool
     */
    public function handle(DiscordMessageReceived $event): bool
    {
        \Log::info(\sprintf(
            'HandleDiscordMessage: Received Discord command from %s (%s.%s): %s',
            $event->user->tag,
            $event->server->name,
            // @phpstan-ignore-next-line
            $event->channel->name,
            $event->content
        ));
        $args = \explode(' ', $event->content);

        if (1 === \preg_match('/\d+d\d+/i', $args[0])) {
            $roll = new \App\Rolls\Generic($event->content, $event->user->tag);
            $event->channel->send($roll->forDiscord());
            //RollEvent::dispatch($roll, $event->channel);
            return true;
        }

        try {
            $class = \sprintf(
                '\\App\Http\\Responses\\Discord\\%sResponse',
                \ucfirst($args[0])
            );
            $response = new $class($event);
            $event->channel->send((string)$response);
        } catch (\Error $ex) {
            \Log::debug('HandleDiscordMessage: ' . $ex->getMessage());
            $event->channel->send('That doesn\'t appear to be a valid command!');
        }
        return true;
    }
}
