<?php

declare(strict_types=1);

namespace Modules\Transformers\Models;

use RuntimeException;
use Stringable;

use function ucfirst;

readonly class AltMode implements Stringable
{
    public const string TYPE_MACHINE = 'machine';
    public const string TYPE_PRIMITIVE = 'primitive';
    public const string TYPE_VEHICLE = 'vehicle';
    public const string TYPE_WEAPON = 'weapon';

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(public string $mode)
    {
        $valid_types = [
            self::TYPE_MACHINE,
            self::TYPE_PRIMITIVE,
            self::TYPE_VEHICLE,
            self::TYPE_WEAPON,
        ];
        if (!in_array($mode, $valid_types, true)) {
            throw new RuntimeException('Invalid alt mode type');
        }
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
        // @phpstan-ignore match.unhandled
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
