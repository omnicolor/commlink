<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Projectors;

use Modules\Shadowrun5e\Events\PhysicalDamageTaken;
use Modules\Shadowrun5e\Events\StunDamageHealed;
use Modules\Shadowrun5e\Events\StunDamageTaken;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

use function event;
use function floor;
use function max;

class StunMonitorProjector extends Projector
{
    private static bool $reset = false;

    public function onStartingEventReplay(): void
    {
        self::$reset = true;
    }

    public function onStunDamageTaken(StunDamageTaken $event): void
    {
        $character = $event->character;
        if (self::$reset) {
            $character->damageStun = 0;
            self::$reset = false;
        }

        // See if the damage will fill up the character's stun track.
        if ($character->damageStun + $event->amount <= $character->stun_monitor) {
            // Nope, the character can take it without overflow.
            $character->damageStun += $event->amount;
            $character->save();
            return;
        }

        // It will overflow the stun monitor.
        $overflow = $character->damageStun + $event->amount - $character->stun_monitor;
        // Fill the stun monitor...
        $character->damageStun = $character->stun_monitor;
        $character->save();

        // See how far past the stun monitor the damage goes,
        // which rolls over to physical at half rate.
        $overflow = (int)floor($overflow / 2);
        if (1 <= $overflow) {
            event(new PhysicalDamageTaken($character, $overflow));
        }
    }

    public function onStunDamageHealed(StunDamageHealed $event): void
    {
        if (self::$reset) {
            $event->character->damageStun = 0;
            self::$reset = false;
        }

        // Remove the damage from the character's stun, stopping at zero.
        $event->character->damageStun = max(
            $event->character->damageStun - $event->amount,
            0,
        );
        $event->character->save();
    }
}
