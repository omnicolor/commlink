<?php

declare(strict_types=1);

namespace Modules\Stillfleet\ValueObjects;

use Override;
use Stringable;

/**
 * @property int $base_rate
 * @property int $score_bonus
 * @property int $total_pool
 */
readonly class Level implements Stringable
{
    public function __construct(public int $level)
    {
    }

    public function __get(string $name): mixed
    {
        if ('base_rate' === $name) {
            return $this->getBaseRate();
        }
        if ('score_bonus' === $name) {
            return $this->getScoreBonus();
        }
        if ('total_pool' === $name) {
            return $this->getTotalPool();
        }
        return null;
    }

    #[Override]
    public function __toString(): string
    {
        return (string)$this->level;
    }

    private function getBaseRate(): int
    {
        return $this->level * 25;
    }

    private function getScoreBonus(): int
    {
        return (int)floor($this->level / 5);
    }

    private function getTotalPool(): int
    {
        return ($this->level - 1) * 6;
    }
}
