<?php

declare(strict_types=1);

namespace Modules\Alien\Projectors;

use Modules\Alien\Events\MoneyGained;
use Modules\Alien\Events\MoneySpent;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class CashProjector extends Projector
{
    private static bool $reset = false;

    public function onStartingEventReplay(): void
    {
        self::$reset = true;
    }

    public function onMoneyGained(MoneyGained $event): void
    {
        $character = $event->character;
        if (self::$reset) {
            $character->cash = 0;
            self::$reset = false;
        }

        $character->cash += $event->amount;
        $character->save();
    }

    public function onMoneySpent(MoneySpent $event): void
    {
        $character = $event->character;
        if (self::$reset) {
            $character->cash = 0;
            self::$reset = false;
        }

        $character->cash -= $event->amount;
        $character->save();
    }
}
