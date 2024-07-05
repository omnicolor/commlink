<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use DateTimeInterface;

/**
 * Karma log stores all karma gain and spent, including during character
 * generation.
 * @psalm-suppress PossiblyUnusedProperty
 */
class KarmaLogEntry
{
    public function __construct(
        public string $description,
        public int $karma,
        public ?DateTimeInterface $realDate = null,
        public ?DateTimeInterface $gameDate = null,
    ) {
    }
}
