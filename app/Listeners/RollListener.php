<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\RollEvent;
use App\Models\Slack\Channel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Class to listen for rolls.
 */
class RollListener
{
    /**
     * Handle the event.
     * @param object $event
     */
    public function handleRoll($event): void
    {
        switch (get_class($event->source)) {
            case Channel::class:
                \Log::info(sprintf(
                    '%s in Slack (%s — %s)',
                    $event->title,
                    $event->source->channel,
                    $event->source->team
                ));
                break;
            default:
                \Log::info(sprintf(
                    '%s in unknown service',
                    $event->title,
                ));
                break;
        }
    }

    /**
     * Register the listeners for the subscriber.
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events): void
    {
        $events->listen(
            RollEvent::class,
            RollListener::class . '@handleRoll'
        );
    }
}
