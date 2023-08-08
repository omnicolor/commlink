<?php

declare(strict_types=1);

namespace App\Models\Transformers;

class AltMode
{
    public const TYPE_MACHINE = 'machine';
    public const TYPE_PRIMITIVE = 'primitive';
    public const TYPE_VEHICLE = 'vehicle';
    public const TYPE_WEAPON = 'weapon';

    public function __construct(public string $mode)
    {
    }

    public function statisticModifier(string $statistic): ?int
    {
        $mode = match ($this->mode) {
            self::TYPE_MACHINE => [
                'courage' => 0,
                'endurance' => 2,
                'firepower' => null,
                'intelligence' => 0,
                'rank' => 0,
                'skill' => 2,
                'speed' => -2,
                'strength' => 1,
            ],
            self::TYPE_PRIMITIVE => [
                'courage' => 0,
                'endurance' => 1,
                'firepower' => null,
                'intelligence' => 0,
                'rank' => 0,
                'skill' => -3,
                'speed' => 2,
                'strength' => 3,
            ],
            self::TYPE_VEHICLE => [
                'courage' => 1,
                'endurance' => -2,
                'firepower' => null,
                'intelligence' => 2,
                'rank' => 0,
                'skill' => 0,
                'speed' => 4,
                'strength' => -2,
            ],
            self::TYPE_WEAPON => [
                'courage' => 2,
                'endurance' => -2,
                'firepower' => 1,
                'intelligence' => 0,
                'rank' => 0,
                'skill' => 2,
                'speed' => 0,
                'strength' => null,
            ],
        };
        return $mode[$statistic];
    }
}
