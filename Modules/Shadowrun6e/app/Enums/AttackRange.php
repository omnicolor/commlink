<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Enums;

enum AttackRange: string
{
    case Close = 'close';
    case Near = 'near';
    case Medium = 'medium';
    case Far = 'far';
    case Extreme = 'extreme';

    public static function fromMeters(int $range): self
    {
        return match (true) {
            $range <= 3 => self::Close,
            $range <= 50 => self::Near,
            $range <= 250 => self::Medium,
            $range <= 500 => self::Far,
            default => self::Extreme,
        };
    }
}
