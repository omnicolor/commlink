<?php

declare(strict_types=1);

namespace Modules\Transformers\Models;

use Stringable;

use function ucfirst;

class AltMode implements Stringable
{
    public const TYPE_MACHINE = 'machine';
    public const TYPE_PRIMITIVE = 'primitive';
    public const TYPE_VEHICLE = 'vehicle';
    public const TYPE_WEAPON = 'weapon';

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(public string $mode)
    {
    }

    public function __toString(): string
    {
        return ucfirst($this->mode);
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function statisticModifier(string $statistic): ?int
    {
        // @phpstan-ignore-next-line
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
