<?php

declare(strict_types=1);

namespace Modules\Avatar\Enums;

use function array_column;

/**
 * @codeCoverageIgnore
 */
enum Era: string
{
    case Aang = 'aang';
    case HundredYearWar = 'hundred-year-war';
    case Korra = 'korra';
    case Kyoshi = 'kyoshi';
    case Roku = 'roku';

    public function description(): string
    {
        return match ($this) {
            Era::Aang => 'The Aang Era is set after the events of the '
                . 'Imbalance comics trilogy, some time after the end of '
                . 'Avatar: The Last Airbender. Play in the Aang Era if you '
                . 'want to heal the world after tragedy and help push it into '
                . 'a brighter future.',
            Era::HundredYearWar => 'The Hundred Year War Era focuses on the '
                . 'time just before Avatar Aang’s awakening at the beginning '
                . 'of Avatar: The Last Airbender. Play in the Hundred Year War '
                . 'Era if you want to rebel against unjust rule, protect the '
                . 'weak, and stand up to tyranny.',
            Era::Korra => 'The Korra Era covers a period that takes place '
                . 'after the events of the Ruins of the Empire comic trilogy, '
                . 'some time after the end of The Legend of Korra. Play in the '
                . 'Korra Era if you want to deal with the repercussions of '
                . 'imperialism and play in a modernized era.',
            Era::Kyoshi => 'The Kyoshi Era covers the events right after The '
                . 'Shadow of Kyoshi novel. Play in the Kyoshi Era if you want '
                . 'to fight in battles against rogues and bandits and deal '
                . 'with governmental corruption as the nations establish their '
                . 'borders.',
            Era::Roku => 'The Roku Era covers the time right after Sozin '
                . 'became Fire Lord and before Roku married. Play in the Roku '
                . 'Era if you want to deal with tensions between different '
                . 'nations and the trials of maintaining an uneasy peace.',
        };
    }

    public function name(): string
    {
        return match ($this) {
            Era::Aang => 'The Aang Era',
            Era::HundredYearWar => 'The Hundred Year War Era',
            Era::Korra => 'The Korra Era',
            Era::Kyoshi => 'The Kyoshi Era',
            Era::Roku => 'The Roku Era',
        };
    }

    /**
     * Return the values for the enumeration.
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
