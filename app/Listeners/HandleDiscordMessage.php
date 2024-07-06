<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\DiscordMessageReceived;
use App\Events\RollEvent;
use App\Models\Channel;
use App\Rolls\Roll;
use Error;
use Illuminate\Support\Facades\Log;
use Nwidart\Modules\Facades\Module;

use function explode;
use function is_numeric;
use function preg_match;
use function sprintf;
use function ucfirst;

class HandleDiscordMessage
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function handle(DiscordMessageReceived $event): bool
    {
        $args = explode(' ', $event->content);

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
        $channel->user = (string)$event->user->id;
        $channel->username = optional($event->user)->displayname;

        // See if the requested roll is XdY or something similar.
        if (1 === preg_match('/\d+d\d+/i', $args[0])) {
            $roll = new \App\Rolls\Generic(
                $event->content,
                $channel->username,
                $channel
            );
            $event->channel->sendMessage($roll->forDiscord());
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
                    /** @var Roll */
                    $roll = new $class(
                        $event->content,
                        optional($event->user)->username,
                        $channel,
                        $event,
                    );

                    /** @psalm-suppress TooManyTemplateParams */
                    $event->message->reply($roll->forDiscord());
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
                /** @var Roll */
                $roll = new $class(
                    $event->content,
                    optional($event->user)->username,
                    $channel
                );
                // @phpstan-ignore-next-line
                $event->channel->sendMessage($roll->forDiscord());

                if ('help' !== $args[0]) {
                    RollEvent::dispatch($roll, $channel);
                }
                return true;
            } catch (Error) { // @codeCoverageIgnore
                // Ignore errors here, they might want a generic command.
            }
        }

        // See if the roll is just a number, and if there's a number-only
        // handler for the registered system.
        // TODO: Remove this when all systems are modularized.
        if (is_numeric($args[0]) && null !== $channel->system) {
            try {
                $class = sprintf(
                    '\\App\Rolls\\%s\\Number',
                    ucfirst($channel->system),
                );
                /** @var Roll */
                $roll = new $class(
                    $event->content,
                    optional($event->user)->username,
                    $channel,
                    $event
                );

                /**
                 * @psalm-suppress TooManyTemplateParams
                 */
                $event->message->reply($roll->forDiscord());
                RollEvent::dispatch($roll, $channel);
                return true;
            } catch (Error) {
                // Ignore errors here, they might want a generic command.
            }
        }

        // Try system-specific rolls that aren't numeric.
        // TODO: Remove this when all systems are modularized.
        try {
            $class = sprintf(
                '\\App\\Rolls\\%s\\%s',
                ucfirst($channel->system ?? 'Unknown'),
                ucfirst($args[0])
            );
            /** @var Roll */
            $roll = new $class(
                $event->content,
                optional($event->user)->username,
                $channel
            );
            // @phpstan-ignore-next-line
            $event->channel->sendMessage($roll->forDiscord());

            if ('help' !== $args[0]) {
                RollEvent::dispatch($roll, $channel);
            }
            return true;
        } catch (Error $ex) {
            // Again, ignore errors, they might want a generic command.
        }

        // Try generic rolls.
        try {
            $class = sprintf('\\App\\Rolls\\%s', ucfirst($args[0]));
            /** @var Roll */
            $roll = new $class(
                $event->content,
                optional($event->user)->username ?? optional($event->user)->displayname,
                $channel,
                $event
            );
            // @phpstan-ignore-next-line
            $event->channel->sendMessage($roll->forDiscord());
            return true;
        } catch (Error) {
            // Again, ignore errors, they might want an old-school response.
        }

        // Try an old-format HTTP Response
        try {
            $class = sprintf(
                '\\App\\Http\\Responses\\Discord\\%sResponse',
                ucfirst($args[0])
            );
            /** @psalm-suppress InvalidCast */
            // @phpstan-ignore-next-line
            $response = (string)(new $class($event));
            if ('' !== $response) {
                $event->channel->sendMessage($response);
            }
        } catch (Error $ex) {
            Log::debug(
                '{system} - Could not find roll "{roll}" from user "{user}"',
                [
                    'system' => $channel->system,
                    'roll' => ucfirst($args[0]),
                    'user' => $channel->username,
                    'exception' => $ex->getMessage(),
                ],
            );
            $event->channel->sendMessage('That doesn\'t appear to be a valid command!');
        }
        return true;
    }
}
