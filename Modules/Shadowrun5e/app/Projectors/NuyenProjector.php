<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Projectors;

use Modules\Shadowrun5e\Events\NuyenGained;
use Modules\Shadowrun5e\Events\NuyenSpent;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class NuyenProjector extends Projector
{
    private static bool $reset = false;

    public function onStartingEventReplay(): void
    {
        self::$reset = true;
    }

    public function onNuyenGained(NuyenGained $event): void
    {
        $character = $event->character;
        if (self::$reset) {
            $character->nuyen = 0;
            self::$reset = false;
        }

        $character->nuyen += $event->amount;
        $character->save();
    }

    public function onNuyenSpent(NuyenSpent $event): void
    {
        $character = $event->character;
        if (self::$reset) {
            $character->nuyen = 0;
            self::$reset = false;
        }

        $character->nuyen -= $event->amount;
        $character->save();
    }
}
