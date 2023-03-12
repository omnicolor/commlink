<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\IrcMessageReceived;

class HandleIrcMessage
{
    /**
     * Handle the event.
     * @param IrcMessageReceived $event
     * @psalm-suppress PossiblyUnusedMethod
     * @return bool
     */
    public function handle(IrcMessageReceived $event): bool
    {
        $event->client->say('#commlink', 'You said "' . $event->content . '".');
        return true;
    }
}
