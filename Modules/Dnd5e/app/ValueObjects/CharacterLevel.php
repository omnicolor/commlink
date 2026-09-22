<?php

declare(strict_types=1);

namespace Modules\Dnd5e\ValueObjects;

use OutOfRangeException;
use Override;
use Stringable;

readonly class CharacterLevel implements Stringable
{
    public int $level;

    public function __construct(public int $experience)
    {
        if (0 > $experience) {
            throw new OutOfRangeException('Experience must be a positive integer');
        }
        $this->level = match (true) {
            $experience >= 355_000 => 20,
            $experience >= 305_000 => 19,
            $experience >= 265_000 => 18,
            $experience >= 225_000 => 17,
            $experience >= 195_000 => 16,
            $experience >= 165_000 => 15,
            $experience >= 140_000 => 14,
            $experience >= 120_000 => 13,
            $experience >= 100_000 => 12,
            $experience >= 85_000 => 11,
            $experience >= 64_000 => 10,
            $experience >= 48_000 => 9,
            $experience >= 34_000 => 8,
            $experience >= 23_000 => 7,
            $experience >= 14_000 => 6,
            $experience >= 6_000 => 5,
            $experience >= 2_700 => 4,
            $experience >= 900 => 3,
            $experience >= 300 => 2,
            default => 1,
        };
    }

    #[Override]
    public function __toString(): string
    {
        return (string)$this->level;
    }

    public static function make(int $level): CharacterLevel
    {
        return new self(match ($level) {
            20 => 355_000,
            19 => 305_000,
            18 => 265_000,
            17 => 225_000,
            16 => 195_000,
            15 => 165_000,
            14 => 140_000,
            13 => 120_000,
            12 => 100_000,
            11 => 85_000,
            10 => 64_000,
            9 => 48_000,
            8 => 34_000,
            7 => 23_000,
            6 => 14_000,
            5 => 6_500,
            4 => 2_700,
            3 => 900,
            2 => 300,
            1 => 0,
            default => throw new OutOfRangeException('Level must be between 1 and 20'),
        });
    }
}
