<?php

declare(strict_types=1);

namespace Modules\Dnd5e\Projectors;

use Modules\Dnd5e\Events\ExperienceAdded;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ExperienceProjector extends Projector
{
    private static bool $reset = false;

    public function onStartingEventReplay(): void
    {
        self::$reset = true;
    }

    public function onExperienceAdded(ExperienceAdded $event): void
    {
        if (self::$reset) {
            $event->character->experience_points = 0;
            self::$reset = false;
        }

        $event->character->experience_points += $event->amount;
        $event->character->save();
    }
}
