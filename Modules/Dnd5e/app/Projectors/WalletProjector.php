<?php

declare(strict_types=1);

namespace Modules\Dnd5e\Projectors;

use Modules\Dnd5e\Events\MoneyConverted;
use Modules\Dnd5e\Events\MoneyGained;
use Modules\Dnd5e\Events\MoneySpent;
use Modules\Dnd5e\ValueObjects\Wallet;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class WalletProjector extends Projector
{
    private static bool $reset = false;

    public function onStartingEventReplay(): void
    {
        self::$reset = true;
    }

    public function onMoneyGained(MoneyGained $event): void
    {
        if (self::$reset) {
            $event->character->wallet = Wallet::make();
            self::$reset = false;
        }
        $event->character->wallet->add($event->currency, $event->amount);
        $event->character->save();
    }

    public function onMoneySpent(MoneySpent $event): void
    {
        if (self::$reset) {
            $event->character->wallet = Wallet::make();
            self::$reset = false;
        }
        $event->character->wallet->subtract($event->currency, $event->amount);
        $event->character->save();
    }

    public function onMoneyConverted(MoneyConverted $event): void
    {
        if (self::$reset) {
            $event->character->wallet = Wallet::make();
            self::$reset = false;
        }

        $event->character->wallet->subtract($event->from_currency, $event->from_amount);
        $event->character->wallet->add($event->to_currency, $event->to_amount);
        $event->character->save();
    }
}
