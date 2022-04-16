<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\DiscordMessageReceived;
use App\Events\RollEvent;
use App\Models\Channel;
use App\Rolls\Roll;

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
        if (null === $channel) {
            $channel = new Channel([
                'channel_id' => $textChannel->id,
                'server_id' => $event->server->id,
                'type' => Channel::TYPE_DISCORD,
            ]);
        }
        // @phpstan-ignore-next-line
        $channel->user = (string)$event->user->id;
        $channel->username = $event->user->username;

        // See if the requested roll is XdY or something similar.
        if (1 === \preg_match('/\d+d\d+/i', $args[0])) {
            $roll = new \App\Rolls\Generic(
                $event->content,
                $event->user->username,
                $channel
            );
            $event->channel->sendMessage($roll->forDiscord());
            RollEvent::dispatch($roll, $channel);
            return true;
        }

        // See if the roll is just a number, and if there's a number-only
        // handler for the registered system.
        if (\is_numeric($args[0]) && null !== $channel->system) {
            try {
                $class = \sprintf(
                    '\\App\Rolls\\%s\\Number',
                    ucfirst($channel->system),
                );
                /** @var Roll */
                $roll = new $class(
                    $event->content,
                    $event->user->username,
                    $channel
                );
                $event->channel->sendMessage($roll->forDiscord());
                RollEvent::dispatch($roll, $channel);
                return true;
            } catch (\Error $ex) {
                // Ignore.
                \Log::debug($ex->getMessage());
            }
        }

        // Try system-specific rolls that aren't numeric.
        try {
            $class = \sprintf(
                '\\App\\Rolls\\%s\\%s',
                \ucfirst($channel->system ?? 'Unknown'),
                \ucfirst($args[0])
            );
            /** @var Roll */
            $roll = new $class($event->content, $event->user->username, $channel);
            $event->channel->sendMessage($roll->forDiscord());

            if ('help' !== $args[0]) {
                RollEvent::dispatch($roll, $channel);
            }
            return true;
        } catch (\Error $ex) {
            \Log::debug($ex->getMessage());
        }

        // Try generic rolls.
        try {
            $class = \sprintf('\\App\\Rolls\\%s', \ucfirst($args[0]));
            /** @var Roll */
            $roll = new $class($event->content, $event->user->username, $channel);
            $event->channel->sendMessage($roll->forDiscord());
            return true;
        } catch (\Error $ex) {
            \Log::debug($ex->getMessage());
        }

        // Try an old-format HTTP Response
        try {
            $class = \sprintf(
                '\\App\Http\\Responses\\Discord\\%sResponse',
                \ucfirst($args[0])
            );
            $response = new $class($event);
            $event->channel->sendMessage((string)$response);
        } catch (\Error $ex) {
            \Log::debug($ex->getMessage());
            $event->channel->sendMessage('That doesn\'t appear to be a valid command!');
        }
        return true;
    }
}
