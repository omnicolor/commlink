<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use DateTimeInterface;

/**
 * Karma log stores all karma gain and spent, including during character
 * generation.
 */
final class KarmaLogEntry
{
    public function __construct(
        public readonly string $description,
        public readonly int $karma,
        public readonly ?DateTimeInterface $realDate = null,
        public readonly ?DateTimeInterface $gameDate = null,
    ) {
    }
}
