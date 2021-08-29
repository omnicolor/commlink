<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\DiscordMessageReceived;
use App\Events\RollEvent;
use App\Models\Channel;

class HandleDiscordMessage
{
    /**
     * Handle the event.
     * @param DiscordMessageReceived $event
     * @return bool
     */
    public function handle(DiscordMessageReceived $event): bool
    {
        $args = \explode(' ', $event->content);

        /** @var \CharlotteDunois\Yasmin\Models\TextChannel */
        $textChannel = $event->channel;

        $channel = Channel::discord()
            ->where('channel_id', $textChannel->id)
            ->where('server_id', $event->server->id)
            ->first();

        if (1 === \preg_match('/\d+d\d+/i', $args[0])) {
            $roll = new \App\Rolls\Generic($event->content, $event->user->tag);
            $event->channel->send($roll->forDiscord());
            RollEvent::dispatch($roll, $channel);
            return true;
        }

        if (
            \is_numeric($args[0])
            && null !== $channel
            && null !== $channel->system
        ) {
            try {
                $class = \sprintf(
                    '\\App\Rolls\\%s\\Number',
                    ucfirst($channel->system),
                );
                $roll = new $class($event->content, $event->user->tag);
                $event->channel->send($roll->forDiscord());
                RollEvent::dispatch($roll, $channel);
                return true;
            } catch (\Error $ex) {
                // Ignore.
                \Log::debug($ex->getMessage());
            }
        }

        try {
            $class = \sprintf(
                '\\App\Http\\Responses\\Discord\\%sResponse',
                \ucfirst($args[0])
            );
            $response = new $class($event);
            $event->channel->send((string)$response);
        } catch (\Error $ex) {
            $event->channel->send('That doesn\'t appear to be a valid command!');
        }
        return true;
    }
}
