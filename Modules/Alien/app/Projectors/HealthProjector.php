<?php

declare(strict_types=1);

namespace Modules\Alien\Projectors;

use Modules\Alien\Events\DamageHealed;
use Modules\Alien\Events\DamageTaken;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

use function min;

class HealthProjector extends Projector
{
    private static bool $reset = false;

    public function onStartingEventReplay(): void
    {
        self::$reset = true;
    }

    public function onDamageHealed(DamageHealed $event): void
    {
        $character = $event->character;
        if (self::$reset) {
            $character->health_current = $character->health_maximum;
            self::$reset = false;
        }

        $character->health_current = min(
            $character->health_current + $event->amount,
            $character->health_maximum,
        );
        $character->save();
    }

    public function onDamageTaken(DamageTaken $event): void
    {
        $character = $event->character;
        if (self::$reset) {
            $character->health_current = $character->health_maximum;
            self::$reset = false;
        }

        $character->health_current = max(
            $character->health_current - $event->amount,
            0,
        );
        $character->save();
    }
}
