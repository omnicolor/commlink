<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Enums;

/**
 * @codeCoverageIgnore
 */
enum TechComplexity: string
{
    case Simple = 'simple';
    case Standard = 'standard';
    case Complex = 'complex';
    case Corporate = 'corporate';
    case Military = 'military';
    case Godlike = 'godlike';

    public function description(): string
    {
        return match ($this) {
            self::Simple => 'A result of 5+ is a success. A simple archaetech '
                . 'item is a trifle—something that offers a +1 on a roll or '
                . 'does d6 damage. A barred door.',
            self::Standard => '6+ is a success. A utility item (a pair of '
                . 'walkie-talkies) or basic weapon (d8 to 2d8 damage). A '
                . 'locked door.',
            self::Complex => '9+ is a success. A comm, servant robot, sensor '
                . 'network, or advanced weapon (2d20 damage). A door with a '
                . 'magnetic lock and a security cam.',
            self::Corporate => '12+ is a success. A vial of the naniteinfused '
                . 'Blood or a strong-force rifle (kills instantly). The '
                . 'bio-imprint nodule on the secure entry-orifice of a Wetan '
                . 'Mafia hard site.',
            self::Military => '15+ is a success. A starship, or a battlemech '
                . 'with a railgun (levels a block of a city). The AI '
                . 'controlling an advanced government weapons lab, long empty '
                . 'of humans.',
            self::Godlike => 'A roll of 20+ is required. An arkship, or a '
                . 'warmachine more than 1-kilometer (km) tall (capable of '
                . 'geoclimatic, planetary-level violence). The prismatic '
                . 'membrane of a Snakeman inquisitionthrone (you don’t even '
                . 'know what this is).',
        };
    }

    public function minimumRoll(): int
    {
        return match ($this) {
            self::Simple => 5,
            self::Standard => 6,
            self::Complex => 9,
            self::Corporate => 12,
            self::Military => 15,
            self::Godlike => 20,
        };
    }
}
