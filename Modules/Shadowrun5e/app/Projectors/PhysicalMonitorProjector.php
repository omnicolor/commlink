<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Projectors;

use Modules\Shadowrun5e\Events\OverflowDamageTaken;
use Modules\Shadowrun5e\Events\PhysicalDamageHealed;
use Modules\Shadowrun5e\Events\PhysicalDamageTaken;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

use function event;
use function max;

class PhysicalMonitorProjector extends Projector
{
    private static bool $reset = false;

    public function onStartingEventReplay(): void
    {
        self::$reset = true;
    }

    public function onPhysicalDamageTaken(PhysicalDamageTaken $event): void
    {
        $character = $event->character;
        if (self::$reset) {
            $character->damagePhysical = 0;
            $character->damageOverflow = 0;
            self::$reset = false;
        }

        if ($character->damagePhysical + $event->amount < $character->physical_monitor) {
            // Damage won't overflow.
            $character->damagePhysical += $event->amount;
            $character->save();
            return;
        }

        $overflow = $character->damagePhysical + $event->amount - $character->physical_monitor;
        $character->damagePhysical = $character->physical_monitor;
        $character->save();
        event(new OverflowDamageTaken($character, $overflow));
    }

    public function onPhysicalDamageHealed(PhysicalDamageHealed $event): void
    {
        $character = $event->character;
        if (self::$reset) {
            $character->damagePhysical = 0;
            $character->damageOverflow = 0;
            self::$reset = false;
        }

        $amount = $event->amount;

        // Start healing from the overflow track if needed.
        if (0 < $character->damageOverflow && $amount <= $character->damageOverflow) {
            // Healing just happens in overflow.
            $character->damageOverflow = max(
                $character->damageOverflow - $amount,
                0,
            );
            $character->save();
            return;
        }

        if (0 < $character->damageOverflow) {
            // Healing will cover all overflow and roll over to physical.
            $amount -= $character->damageOverflow;
            $character->damageOverflow = 0;
        }

        $character->damagePhysical = max(
            $character->damagePhysical - $amount,
            0,
        );
        $character->save();
    }

    public function onOverflowDamageTaken(OverflowDamageTaken $event): void
    {
        $character = $event->character;
        if (self::$reset) {
            $character->damagePhysical = 0;
            $character->damageOverflow = 0;
            self::$reset = false;
        }

        $character->damageOverflow += $event->amount;
        $character->save();
    }
}
