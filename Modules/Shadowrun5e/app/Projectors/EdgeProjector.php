<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Projectors;

use Modules\Shadowrun5e\Events\EdgeGained;
use Modules\Shadowrun5e\Events\EdgeSpent;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class EdgeProjector extends Projector
{
    private static bool $reset = false;

    public function onStartingEventReplay(): void
    {
        self::$reset = true;
    }

    public function onEdgeBurned(): void
    {
    }

    public function onEdgeGained(EdgeGained $event): void
    {
        $character = $event->character;
        if (self::$reset) {
            $character->edgeCurrent = $character->edge;
            self::$reset = false;
        }

        $character->edgeCurrent++;
        $character->save();
    }

    public function onEdgeSpent(EdgeSpent $event): void
    {
        $character = $event->character;
        if (self::$reset) {
            $character->edgeCurrent = $character->edge;
            self::$reset = false;
        }

        $character->edgeCurrent--;
        $character->save();
    }
}
